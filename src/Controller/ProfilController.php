<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    #[Route('/user/photo', name: 'photo', methods: ['POST', 'OPTIONS'])]
    public function updatePhoto(Request $request, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(["status" => "error", "message" => "Non autorisé"], 401);
        }

        $token = substr($authHeader, 7);
        $user = $userRepo->findOneBy(["token" => $token]);

        if (!$user) {
            return $this->json(["status" => "error", "message" => "Utilisateur non trouvé"], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (!isset($data['image_profil']) || empty($data['image_profil'])) {
            return $this->json(["status" => "error", "message" => "Lien image_profil manquant"], 400);
        }

        $user->setImageProfil($data['image_profil']);

        $em->persist($user);
        $em->flush();

        return $this->json([
            "status" => "ok",
            "message" => "Photo mise à jour",
            "result" => $user
        ], 200, [], ['groups' => ['user:sign']]);
    }
}
