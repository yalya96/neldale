<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;


class AuthentificationController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login()
    {
         $request=new Request();
        // $user = $this->getUser();
        // return $this->json([
        //     'username' => $user->getUsername(),
        //     'roles' => $user->getRoles()
        // ]);
    }
}
