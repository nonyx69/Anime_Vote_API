<?php

namespace App\Controller;

use App\Entity\Choix;
use App\Entity\Questions;
use App\Entity\Sondages;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminController extends AbstractController
{

    #[Route('/api/admin/sondage', name: 'admin_sondage_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em,UserRepository $userRepo): Response
    {

        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $userRepo->findOneBy(['token' => $token]);

        if (!$user || !in_array('ROLE_ADMIN', $user->getRoles_admin())) {
            return $this->json([
                'error' => 'acces refuse tu doit etre admin'
            ], 403);
        }

        $data = json_decode($request->getContent(), true);

        $sondage = new Sondages();
        $sondage->setName($data['name']);
        $sondage->setIsActive($data['visible'] ?? false);

        $question = new Questions();
        $question->setLabel($data['question_label']);
        $question->setMultiple(false);
        $question->setSondage($sondage);

        foreach ($data['choices'] as $choiceLabel) {
            $choix = new Choix();
            $choix->setLabel($choiceLabel);
            $choix->setQuestions($question);
            $em->persist($choix);
        }

        $em->persist($sondage);
        $em->persist($question);
        $em->flush();

        return $this->json([
            'status' => 'Sondage QCM créé !'
        ], 201);
    }
}
