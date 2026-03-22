<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfilController extends AbstractController
{
    private function getAuthenticatedUser(Request $request, UserRepository $userRepo)
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        $token = substr($authHeader, 7);
        return $userRepo->findOneBy(["token" => $token]);
    }

    #[Route('/user/photo', name: 'update_photo', methods: ['POST', 'OPTIONS'])]
    public function updatePhoto(Request $request, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getAuthenticatedUser($request, $userRepo);
        if (!$user) return $this->json(["status" => "error", "message" => "Non autorisé"], 401);

        $data = json_decode($request->getContent(), true);
        if (empty($data['image_profil'])) {
            return $this->json(["status" => "error", "message" => "Lien image_profil manquant"], 400);
        }

        $profil = $user->getProfil();
        $profil->setUser($user);
        $profil->setImageProfil($data['image_profil']);

        $em->persist($profil);
        $em->flush();

        return $this->json(["status" => "ok", "message" => "Photo mise à jour"], 200);
    }

    #[Route('/user/bio', name: 'update_bio', methods: ['POST', 'OPTIONS'])]
    public function updateBio(Request $request, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getAuthenticatedUser($request, $userRepo);
        if (!$user) return $this->json(["status" => "error", "message" => "Non autorisé"], 401);

        $data = json_decode($request->getContent(), true);
        if (empty($data['bio'])) {
            return $this->json(["status" => "error", "message" => "Contenu de la bio manquant"], 400);
        }

        $profil = $user->getProfil();
        $profil->setUser($user);
        $profil->setBio($data['bio']);

        $em->persist($profil);
        $em->flush();

        return $this->json(["status" => "ok", "message" => "Bio mise à jour"], 200);
    }
}
