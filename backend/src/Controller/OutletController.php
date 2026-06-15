<?php

namespace App\Controller;

use App\Entity\Outlet;
use App\Repository\OutletRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/outlets')]
class OutletController extends AbstractController
{
    #[Route('', name: 'api_outlets_list', methods: ['GET'])]
    public function list(OutletRepository $outlets): JsonResponse
    {
        $data = array_map(static fn (Outlet $o): array => [
            'id' => $o->getId(),
            'name' => $o->getName(),
            'address' => $o->getAddress(),
            'city' => $o->getCity(),
            'zipCode' => $o->getZipCode(),
            'latitude' => $o->getLatitude(),
            'longitude' => $o->getLongitude(),
            'phone' => $o->getPhone(),
            'website' => $o->getWebsite(),
        ], $outlets->findBy(['isActive' => true]));

        return new JsonResponse($data);
    }
}
