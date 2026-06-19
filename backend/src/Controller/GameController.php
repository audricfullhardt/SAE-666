<?php

namespace App\Controller;

use App\Entity\ActiveMinigame;
use App\Entity\GamePlayer;
use App\Entity\GameSession;
use App\Entity\User;
use App\Repository\ActiveMinigameRepository;
use App\Repository\GamePlayerRepository;
use App\Repository\GameSessionRepository;
use App\Service\GameService;
use App\Service\MercurePublisher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Uid\Uuid;

#[Route('/api/game')]
class GameController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GameSessionRepository $sessions,
        private readonly GamePlayerRepository $players,
        private readonly ActiveMinigameRepository $activeMinigames,
        private readonly MercurePublisher $mercure,
        private readonly GameService $gameService,
    ) {}

    #[Route('/create', name: 'api_game_create', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function create(#[CurrentUser] User $user): JsonResponse
    {
        $session = new GameSession();
        $session->setCode($this->generateUniqueCode());
        $session->setStatus('waiting');
        $session->setHost($user);

        $host = new GamePlayer();
        $host->setUsername($user->getUsername());
        $host->setPosition(0);
        $host->setTurnOrder(0);
        $host->setIsHost(true);
        $host->setIsReady(false);
        $host->setGameToken(Uuid::v4()->toRfc4122());
        $host->setSession($session);
        $host->setUser($user);

        $this->em->persist($session);
        $this->em->persist($host);
        $this->em->flush();

        return new JsonResponse([
            'sessionId' => $session->getId(),
            'code' => $session->getCode(),
            'host' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
            ],
        ], 201);
    }

    #[Route('/join', name: 'api_game_join', methods: ['POST'])]
    public function join(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];
        $code = strtoupper(trim((string) ($data['code'] ?? '')));
        $username = trim((string) ($data['username'] ?? ''));

        if ($code === '' || $username === '') {
            return new JsonResponse(['error' => 'code et username sont requis'], 400);
        }

        $session = $this->sessions->findOneBy(['code' => $code]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($session->getStatus() !== 'waiting') {
            return new JsonResponse(['error' => 'La partie n\'accepte plus de joueurs'], 409);
        }

        $alreadyTaken = $this->players->findOneBy(['session' => $session, 'username' => $username]);
        if ($alreadyTaken !== null) {
            return new JsonResponse(['error' => 'Ce pseudo est déjà pris dans cette partie'], 409);
        }

        $player = new GamePlayer();
        $player->setUsername($username);
        $player->setPosition(0);
        $player->setTurnOrder(0);
        $player->setIsHost(false);
        $player->setIsReady(false);
        $player->setGameToken(Uuid::v4()->toRfc4122());
        $player->setSession($session);
        $player->setUser(null);

        $this->em->persist($player);
        $this->em->flush();

        $this->mercure->publishToSession($session->getCode(), 'player_joined', [
            'id' => $player->getId(),
            'username' => $player->getUsername(),
            'position' => $player->getPosition(),
            'isHost' => $player->isHost(),
            'isReady' => $player->isReady(),
            'turnOrder' => $player->getTurnOrder(),
        ]);

        return new JsonResponse([
            'gameToken' => $player->getGameToken(),
            'sessionId' => $session->getId(),
            'username' => $player->getUsername(),
            'players' => $this->serializePlayers($session),
        ], 200);
    }

    #[Route('/{code}', name: 'api_game_show', methods: ['GET'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function show(string $code): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        return new JsonResponse([
            'sessionId' => $session->getId(),
            'code' => $session->getCode(),
            'status' => $session->getStatus(),
            'players' => $this->serializePlayers($session),
        ]);
    }

    #[Route('/{code}/start', name: 'api_game_start', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function start(string $code, #[CurrentUser] User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($session->getHost()?->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Seul l\'hôte peut démarrer la partie'], 403);
        }

        $players = $this->players->findBy(['session' => $session]);

        $order = range(1, max(1, count($players)));
        shuffle($order);
        foreach ($players as $i => $player) {
            $player->setTurnOrder($order[$i]);
        }

        $session->setStatus('playing');
        $this->em->flush();

        $this->mercure->publishToSession($session->getCode(), 'game_started', [
            'code' => $session->getCode(),
        ]);

        return new JsonResponse([
            'status' => 'playing',
            'players' => $this->serializePlayers($session),
        ], 200);
    }

    #[Route('/{code}/finish', name: 'api_game_finish', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function finish(string $code, Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($session->getHost()?->getId() !== $user->getId()) {
            return new JsonResponse(['error' => 'Seul l\'hôte peut terminer la partie'], 403);
        }

        if ($session->getStatus() !== 'playing') {
            return new JsonResponse(['error' => 'La partie n\'est pas en cours'], 409);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $winnerId = $data['winnerId'] ?? null;

        if ($winnerId === null) {
            return new JsonResponse(['error' => 'winnerId est requis'], 400);
        }

        $winner = $this->players->findOneBy(['id' => $winnerId, 'session' => $session]);
        if ($winner === null) {
            return new JsonResponse(['error' => 'Gagnant introuvable dans cette partie'], 404);
        }

        $session->setStatus('finished');
        $this->em->flush();

        $this->mercure->publishToSession($session->getCode(), 'game_finished', [
            'winnerId' => $winner->getId(),
            'winnerUsername' => $winner->getUsername(),
            'players' => $this->serializePlayers($session),
        ]);

        return new JsonResponse([
            'status' => 'finished',
            'winner' => [
                'id' => $winner->getId(),
                'username' => $winner->getUsername(),
                'position' => $winner->getPosition(),
            ],
        ], 200);
    }

    #[Route('/{code}/ready', name: 'api_game_ready', methods: ['PATCH'])]
    public function ready(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        $gameToken = $request->headers->get('X-Game-Token');
        $player = null;

        if ($gameToken !== null && $gameToken !== '') {
            $player = $this->players->findOneBy(['session' => $session, 'gameToken' => $gameToken]);
        } elseif ($user !== null) {
            $player = $this->players->findOneBy(['session' => $session, 'user' => $user]);
        } else {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        if ($player === null) {
            return new JsonResponse(['error' => 'Joueur introuvable dans cette partie'], 404);
        }

        $player->setIsReady(true);
        $this->em->flush();

        $this->mercure->publishToSession($session->getCode(), 'player_ready', [
            'id' => $player->getId(),
            'isReady' => true,
        ]);

        return new JsonResponse([
            'playerId' => $player->getId(),
            'isReady' => true,
        ], 200);
    }

    #[Route('/{code}/return', name: 'api_game_return', methods: ['POST'])]
    public function returnToBoard(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($this->resolveCurrentPlayer($session, $request, $user) === null) {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        if ($session->getStatus() !== 'playing') {
            return new JsonResponse(['error' => 'La partie n\'est pas en cours'], 409);
        }

        $this->mercure->publishToSession($session->getCode(), 'return_to_board', []);

        return new JsonResponse(['status' => 'ok'], 200);
    }

    private const MINIGAME_TYPES = ['quiz', 'reflex', 'memory', 'brasdefer', 'gobelets', 'dinofind'];

    #[Route('/{code}/scan', name: 'api_game_scan', methods: ['POST'])]
    public function scan(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($this->resolveCurrentPlayer($session, $request, $user) === null) {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        if ($session->getStatus() !== 'playing') {
            return new JsonResponse(['error' => 'La partie n\'est pas en cours'], 409);
        }

        if ($this->activeMinigames->findOneBy(['session' => $session]) !== null) {
            return new JsonResponse(['error' => 'Un mini-jeu est déjà en cours sur cette partie'], 409);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $challengerId = $data['challengerId'] ?? null;
        $opponentId = $data['opponentId'] ?? null;

        if ($challengerId === null || $opponentId === null) {
            return new JsonResponse(['error' => 'challengerId et opponentId sont requis'], 400);
        }

        if ((string) $challengerId === (string) $opponentId) {
            return new JsonResponse(['error' => 'Le challenger et l\'adversaire doivent être différents'], 400);
        }

        $challenger = $this->players->findOneBy(['id' => $challengerId, 'session' => $session]);
        $opponent = $this->players->findOneBy(['id' => $opponentId, 'session' => $session]);
        if ($challenger === null || $opponent === null) {
            return new JsonResponse(['error' => 'Challenger ou adversaire introuvable dans cette partie'], 404);
        }

        $minigameType = self::MINIGAME_TYPES[random_int(0, \count(self::MINIGAME_TYPES) - 1)];

        $active = new ActiveMinigame();
        $active->setSession($session);
        $active->setMinigameType($minigameType);
        $active->setChallenger($challenger);
        $active->setOpponent($opponent);
        $active->setStatus('in_progress');

        $this->em->persist($active);
        $this->em->flush();

        $this->mercure->publishToSession($session->getCode(), 'minigame_triggered', [
            'minigameType' => $minigameType,
            'challengerId' => $challenger->getId(),
            'opponentId' => $opponent->getId(),
            'challengerUsername' => $challenger->getUsername(),
            'opponentUsername' => $opponent->getUsername(),
        ]);

        return new JsonResponse([
            'activeMinigameId' => $active->getId(),
            'minigameType' => $active->getMinigameType(),
            'challenger' => [
                'id' => $challenger->getId(),
                'username' => $challenger->getUsername(),
            ],
            'opponent' => [
                'id' => $opponent->getId(),
                'username' => $opponent->getUsername(),
            ],
        ], 201);
    }

    #[Route('/{code}/minigame/result', name: 'api_game_minigame_result', methods: ['POST'])]
    public function minigameResult(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($this->resolveCurrentPlayer($session, $request, $user) === null) {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        $active = $this->activeMinigames->findOneBy(['session' => $session]);
        if ($active === null) {
            return new JsonResponse(['error' => 'Aucun mini-jeu en cours sur cette partie'], 404);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $winnerId = $data['winnerId'] ?? null;
        $loserId = $data['loserId'] ?? null;

        if ($winnerId === null || $loserId === null) {
            return new JsonResponse(['error' => 'winnerId et loserId sont requis'], 400);
        }

        $winner = $this->players->findOneBy(['id' => $winnerId, 'session' => $session]);
        $loser = $this->players->findOneBy(['id' => $loserId, 'session' => $session]);

        if ($winner === null || $loser === null) {
            return new JsonResponse(['error' => 'winner ou loser introuvable dans cette partie'], 404);
        }

        $payload = $this->gameService->resolveDuel($winner, $loser, $active, $this->em);

        $this->mercure->publishToSession($session->getCode(), 'duel_resolved', $payload);

        $this->mercure->publishToSession($session->getCode(), 'round_finished', [
            'winner' => $payload['winner'],
            'loser' => $payload['loser'],
        ]);

        return new JsonResponse($payload, 200);
    }

    #[Route('/{code}/minigame/tap', name: 'api_game_tap', methods: ['POST'])]
    public function minigameTap(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($this->resolveCurrentPlayer($session, $request, $user) === null) {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        $data = json_decode($request->getContent(), true) ?? [];
        $playerId = $data['playerId'] ?? null;
        $taps = $data['taps'] ?? null;

        if ($playerId === null || $taps === null) {
            return new JsonResponse(['error' => 'playerId et taps sont requis'], 400);
        }

        $this->mercure->publishToSession($session->getCode(), 'player_tap', [
            'playerId' => (int) $playerId,
            'taps' => (int) $taps,
        ]);

        return new JsonResponse(['ok' => true], 200);
    }

    #[Route('/{code}/minigame/current', name: 'api_game_minigame_current', methods: ['GET'])]
    public function minigameCurrent(string $code, Request $request, #[CurrentUser] ?User $user): JsonResponse
    {
        $session = $this->sessions->findOneBy(['code' => strtoupper($code)]);
        if ($session === null) {
            return new JsonResponse(['error' => 'Session introuvable'], 404);
        }

        if ($this->resolveCurrentPlayer($session, $request, $user) === null) {
            return new JsonResponse(['error' => 'Authentification requise (JWT ou X-Game-Token)'], 401);
        }

        $active = $this->activeMinigames->findOneBy(['session' => $session]);
        if ($active === null) {
            return new JsonResponse(['minigame' => null], 200);
        }

        $opponent = $active->getOpponent();

        return new JsonResponse([
            'minigameType' => $active->getMinigameType(),
            'status' => $active->getStatus(),
            'challenger' => [
                'id' => $active->getChallenger()?->getId(),
                'username' => $active->getChallenger()?->getUsername(),
            ],
            'opponent' => $opponent === null ? null : [
                'id' => $opponent->getId(),
                'username' => $opponent->getUsername(),
            ],
            'caseData' => $active->getCaseData(),
        ], 200);
    }

    private function resolveCurrentPlayer(GameSession $session, Request $request, ?User $user): ?GamePlayer
    {
        $gameToken = $request->headers->get('X-Game-Token');

        if ($gameToken !== null && $gameToken !== '') {
            return $this->players->findOneBy(['session' => $session, 'gameToken' => $gameToken]);
        }

        if ($user !== null) {
            return $this->players->findOneBy(['session' => $session, 'user' => $user]);
        }

        return null;
    }

    private function generateUniqueCode(): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        do {
            $code = '';
            for ($i = 0; $i < 6; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while ($this->sessions->findOneBy(['code' => $code]) !== null);

        return $code;
    }

    private function serializePlayers(GameSession $session): array
    {
        $players = $this->players->findBy(['session' => $session], ['turnOrder' => 'ASC', 'id' => 'ASC']);

        return array_map(static fn(GamePlayer $p): array => [
            'id' => $p->getId(),
            'username' => $p->getUsername(),
            'position' => $p->getPosition(),
            'isHost' => $p->isHost(),
            'isReady' => $p->isReady(),
            'turnOrder' => $p->getTurnOrder(),
        ], $players);
    }
}
