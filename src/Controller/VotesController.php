<?php

namespace App\Controller;

use App\Entity\Reponses;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class VotesController extends AbstractController
{
    #[Route('/api/votes', name: 'app_voir', methods: ['GET', 'OPTIONS'])]
    public function voirMesVotes(Request $request, UserRepository $userRepo, EntityManagerInterface $em): Response
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->json(["error" => "Token manquant ou format incorrect"], 401);
        }

        $token = substr($authHeader, 7);
        $user = $userRepo->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur introuvable'], 403);
        }

        $mesReponses = $em->getRepository(Reponses::class)->findBy(
            ['idUser' => (string)$user->getId()], // prend l'id du user dans la table Reponses
            ['id' => 'DESC'] // trie les id dans l'ordre decroissant pour avoir plus récent en 1er
        );

        $resultats = []; // prepare tableau vide

        // faire boucle sur chaque objet "Reponses" trouver
        foreach ($mesReponses as $reponse) {

            // utilise les relations pour recupere les objets liés aux réponses
            $question = $reponse->getQuestion(); // recupere l'objet Question associé
            $choix = $reponse->getChoix(); // recupere l'objet Choix associé

            // recupere le sondage lié a la Question (si qst existe)
            $sondage = $question ? $question->getSondage() : null;

            // créer reponse sous forme tableau
            $resultats[] = [
                'id' => $reponse->getId(),

                // si sondage existe on prend "nom" sinon on affiche un message d'erreur
                'sondage_name' => $sondage ? $sondage->getName() : 'Sondage inconnu',

                // recupere le "titre" de la question
                'question_label' => $question ? $question->getLabel() : 'Question introuvable',

                // recupere le "vote" que l'utilisateur a fait sinon on affiche un message d'erreur
                'choix_label' => $choix ? $choix->getLabel() : 'Choix introuvable',

                // recupere le "commentaire" si l'utilisateur en a laisser un sinon on affiche un message d'erreur
                'message' => $reponse->getMessage(),

                // garder l'id de la question pour aller vers un lien "Mes votes" sur page User
                'question_id' => $question ? $question->getId() : null,
            ];
        }

        return $this->json($resultats);
    }
}
