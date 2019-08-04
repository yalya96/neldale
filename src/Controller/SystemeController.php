<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\Prestataire;
use App\Entity\Compte;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SystemeController extends AbstractController
{
    /**
     * @Route("/systeme", name="systeme")
     */
    public function index()
    {
        return $this->render('systeme/index.html.twig', [
            'controller_name' => 'SystemeController',
        ]);
    }
    /**
     * @Route("/ajout")
     */
    public function addsys(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if(isset($values->username) && isset($values->prenom) && isset($values->nom) && isset($values->telephone)) 
        {
            $a=$this->chiffre($values->prenom);
            $b=$this->chiffre($values->nom);
            if ((is_numeric($values->telephone) && $a==0 && $b==0) && ($values->profil==2 || $values->profil==1))
            {
                    $user= new User();
                    $user->setPrenom(trim($values->prenom));
                    $user->setNom(trim($values->nom));
                    $user->setTelephone(trim($values->telephone));
                    $user->setUsername(trim($values->username));
                    $user->setPassword($passwordEncoder->encodePassword($user, "welcome"));
                    if ($values->profil==1) {
                        $user->setRoles(['ROLE_SUPERADMIN']);    
                    }
                    if ($values->profil==2) {
                        $user->setRoles(['ROLE_CAISSIER']);    
                    }
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $data=[
                        'Message'=>'UTILISATEUR CREER',
                        'Username'=> $user->getUsername(),
                        'MOT DE PASSE'=>'welcome',
                        'ROLES'=> $user->getRoles()
                    ];
                    return new JsonResponse($data, 500);
            }
            else {
                $data = [
                    'status' => 500,
                    'message' => 'UTILISATEUR NONCREER1'
                ];
                return new JsonResponse($data, 500);
            }
             
            
                
        }
            else {
                $data = [
                    'status' => 500,
                    'message' => 'UTILISATEUR NONCREER2'
                ];
                return new JsonResponse($data, 500);
            }
    }
    function chiffre($test)
    {
        $retour=0;
        $taille=strlen($test);
       for ($i=0; $i < $taille; $i++) { 
           if (is_numeric($test[$i])) {
               $retour=1;
                break;
           }
       }
       if ($retour==0) {
           return $retour;
       }
       if ($retour==1) {
           return $retour;
       }
    }
    function numerocompte()
    {
        $jour = date('d');
        $mois = date('m');
        $annee = date('Y');
        $heure = date('H');
        $minute = date('i');
        $seconde = date('s');
        $test=$seconde.$minute.$heure.$annee.$mois.$jour;
        return $test;
    }
}
