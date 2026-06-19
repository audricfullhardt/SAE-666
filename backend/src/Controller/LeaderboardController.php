<?php

namespace App\Controller;

use App\Repository\GameResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/leaderboard')]
class LeaderboardController extends AbstractController
{
    #[Route('', name: 'api_leaderboard', methods: ['GET'])]
    public function index(GameResultRepository $results): JsonResponse
    {
        $qb = $results->createQueryBuilder('gr');

        $winRows = $qb
            ->select('w.username AS username', 'COUNT(gr.id) AS cnt')
            ->join('gr.winner', 'w')
            ->groupBy('w.username')
            ->getQuery()
            ->getResult();

        $lossRows = $results->createQueryBuilder('gr')
            ->select('l.username AS username', 'COUNT(gr.id) AS cnt')
            ->join('gr.loser', 'l')
            ->groupBy('l.username')
            ->getQuery()
            ->getResult();

        $stats = [];
        foreach ($winRows as $row) {
            $stats[$row['username']] ??= ['wins' => 0, 'losses' => 0];
            $stats[$row['username']]['wins'] = (int) $row['cnt'];
        }
        foreach ($lossRows as $row) {
            $stats[$row['username']] ??= ['wins' => 0, 'losses' => 0];
            $stats[$row['username']]['losses'] = (int) $row['cnt'];
        }

        $players = [];
        foreach ($stats as $username => $stat) {
            $wins = $stat['wins'];
            $losses = $stat['losses'];
            $total = $wins + $losses;
            $players[] = [
                'username' => $username,
                'wins' => $wins,
                'losses' => $losses,
                'total' => $total,
                'winrate' => $total === 0 ? 0.0 : round($wins / $total * 100, 1),
            ];
        }

        usort($players, static function (array $a, array $b): int {
            return $b['wins'] <=> $a['wins']
                ?: $b['winrate'] <=> $a['winrate'];
        });

        $players = array_slice($players, 0, 50);

        $leaderboard = [];
        foreach ($players as $i => $player) {
            $leaderboard[] = ['rank' => $i + 1] + $player;
        }

        return new JsonResponse($leaderboard, 200);
    }
}
