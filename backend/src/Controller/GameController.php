<?php

namespace App\Controller;

use App\Entity\GamePlayer;
use App\Entity\GameSession;
use App\Entity\User;
use App\Repository\GamePlayerRepository;
use App\Repository\GameSessionRepository;
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
    ) {
    }

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

        return new JsonResponse([
            'status' => 'playing',
            'players' => $this->serializePlayers($session),
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

        return new JsonResponse([
            'playerId' => $player->getId(),
            'isReady' => true,
        ], 200);
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

        return array_map(static fn (GamePlayer $p): array => [
            'id' => $p->getId(),
            'username' => $p->getUsername(),
            'position' => $p->getPosition(),
            'isHost' => $p->isHost(),
            'isReady' => $p->isReady(),
            'turnOrder' => $p->getTurnOrder(),
        ], $players);
    }
}
