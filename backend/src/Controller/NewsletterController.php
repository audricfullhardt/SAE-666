<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/newsletter')]
class NewsletterController extends AbstractController
{
    #[Route('/subscribe', name: 'api_newsletter_subscribe', methods: ['POST'])]
    public function subscribe(
        Request $request,
        NewsletterRepository $newsletters,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true) ?? [];
        $email = trim((string) ($data['email'] ?? ''));

        if ($email === '' || count($validator->validate($email, new Email())) > 0) {
            return new JsonResponse(['error' => 'Email invalide'], 400);
        }

        if ($newsletters->findOneBy(['email' => $email]) !== null) {
            return new JsonResponse(['error' => 'Cet email est déjà inscrit'], 409);
        }

        $newsletter = new Newsletter();
        $newsletter->setEmail($email);

        $em->persist($newsletter);
        $em->flush();

        return new JsonResponse(['message' => 'Inscription confirmée'], 201);
    }
}
