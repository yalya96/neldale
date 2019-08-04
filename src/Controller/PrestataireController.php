<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PrestataireController extends AbstractController
{
    /**
     * @Route("/prestataire", name="prestataire")
     */
    public function index()
    {
        return $this->render('prestataire/index.html.twig', [
            'controller_name' => 'PrestataireController',
        ]);
    }
}
