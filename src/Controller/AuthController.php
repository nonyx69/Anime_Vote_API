<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AuthController extends AbstractController
{
    public function __construct(private UserRepository $userRepo){}

    #[Route('/user/sign', name: 'app_auth_sign', methods: ['POST'])]
    public function sign(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status"=>"error", "message"=>"données vides"]);
        }

        if ($this->userRepo->findOneBy(['email'=>$data['email']])) {
            return $this->json(["status"=>"error","message"=>"email déjà utilisé"]);
        }

        $newUser = new User();

        $newUser->setEmail($data["email"]);
        $newUser->setPseudo($data["pseudo"]);

        $salt = md5("anime");
        $hashedPassword = md5($salt . $data['password']);
        $newUser->setSalt($salt);
        $newUser->setPassword($hashedPassword);

        $token = md5(uniqid());
        $newUser->setToken($token);

        $newUser->setRoles(["ROLE_USER"]);

        $em->persist($newUser);
        $em->flush();

        return $this->json([
            "status"=>"ok",
            "message"=>"user created",
            "result"=> [
                "id"=>$newUser->getId(),
                "pseudo"=>$newUser->getPseudo(),
                "token"=>$token,
                "email"=>$newUser->getEmail()
            ]
        ]);
    }

    #[Route('/user/login', name: 'app_auth_login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return $this->json(["status"=>"error", "message"=>"données vides"]);
        }

        $user = $this->userRepo->findOneBy(["email"=>$data["email"]]);
        if(!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        $salt = $user->getSalt();
        if( md5($salt . ($data['password'])) === $user->getPassword()){
            return $this->json([
                "status"=>"ok",
                "message"=>"login ok",
                "result"=>[
                    "id"=>$user->getId(),
                    "pseudo"=>$user->getPseudo(),
                    "email"=>$user->getEmail()
                ]
            ]);
        } else {
            return $this->json(["status"=>"error", "message"=>"login failed, wrong password"]);
        }
    }

    #[Route('/user/token', name: 'app_auth_token', methods: ['GET'])]
    public function token(Request $request): Response
    {
        $token = $request->headers->get('Authorization');

        if(!$token){
            return $this->json(["status"=>"error", "message"=>"token not found"]);
        }

        $token = substr($token, 7);

        $user = $this->userRepo->findOneBy(["token"=>$token]);

        if(!$user){
            return $this->json(["status"=>"error", "message"=>"user not found"]);
        }

        return $this->json([
            "status"=>"ok",
            "message"=>"connected",
            "result"=>[
                "id"=>$user->getId(),
                "pseudo"=>$user->getPseudo(),
                "email"=>$user->getEmail()
            ]
        ]);
    }
}
