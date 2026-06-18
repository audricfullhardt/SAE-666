<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercurePublisher
{
    public function __construct(private HubInterface $hub)
    {
    }

    public function publishToSession(string $sessionCode, string $event, array $data): void
    {
        $update = new Update(
            "game/{$sessionCode}",
            json_encode(['event' => $event, 'data' => $data])
        );

        $this->hub->publish($update);
    }
}
