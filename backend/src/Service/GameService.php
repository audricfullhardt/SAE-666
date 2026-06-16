<?php

namespace App\Service;

use App\Entity\ActiveMinigame;
use App\Entity\GamePlayer;
use App\Entity\GameResult;
use Doctrine\ORM\EntityManagerInterface;

class GameService
{
    public function resolveDuel(GamePlayer $winner, GamePlayer $loser, ActiveMinigame $active, EntityManagerInterface $em): array
    {
        $winner->setPosition($winner->getPosition() + 2);
        $loser->setPosition(max(0, $loser->getPosition() - 2));

        $result = new GameResult();
        $result->setSession($active->getSession());
        $result->setMinigameType($active->getMinigameType());
        $result->setWinner($winner);
        $result->setLoser($loser);

        $em->persist($result);
        $em->remove($active);
        $em->flush();

        return [
            'winner' => [
                'id' => $winner->getId(),
                'username' => $winner->getUsername(),
                'position' => $winner->getPosition(),
            ],
            'loser' => [
                'id' => $loser->getId(),
                'username' => $loser->getUsername(),
                'position' => $loser->getPosition(),
            ],
        ];
    }
}
