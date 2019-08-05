<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Prestataire;
use Symfony\Component\HttpFoundation\JsonResponse;
class PrestataireController extends AbstractController
{
    /**
     * @Route("/", name="prestataire")
     */
    public function index()
    {
        return $this->render('prestataire/index.html.twig', [
            'controller_name' => 'PrestataireController',
        ]);
    }
    /**
     * @Route("/ajoutuser")
     */
    public function adduser(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if(isset($values->prenom) && isset($values->nom) && isset($values->telephone)) 
        {
            $a=$this->chiffre($values->prenom);
            $b=$this->chiffre($values->nom);
            if ( (strlen($values->telephone)==9) && !empty($values->prenom) && !empty($values->nom)
                && (is_numeric($values->telephone)) && $a==0 && $b==0
                )
            {
                    $user= new User();
                    $user->setPrenom(trim($values->prenom));
                    $user->setNom(trim($values->nom));
                    $user->setCNI($values->CNI);
                    $user->setAdresse(trim($values->adresse));
                    $user->setTelephone(trim($values->telephone));
                    $user->setStatut("ACTIF");
                    $test=$this->getUser()->getId();
                    $prestataire = $this->getDoctrine()->getRepository(Prestataire::class)->find($test);
                    $b=rand(20,100);
                    $c=$values->prenom[0].$values->prenom[1];
                    $d=rand(0,20);
                    $e=$values->nom[0].$values->nom[1];
                    $f=$test.$b.$c.$d.$e;
                    $user->setUsername($f);
                    $user->setPrest($prestataire);
                    $user->setPassword($passwordEncoder->encodePassword($user, "welcome"));
                    if ($values->profil==1) {
                        $user->setRoles(['ROLE_ADMINPART']);    
                    }
                    elseif ($values->profil==2) {
                        $user->setRoles(['ROLE_USER']);    
                    }
                    $entityManager->persist($user);
                    $entityManager->persist($prestataire);
                    $entityManager->flush();
                    $data=[
                        'Message'=>'UTILISATEUR CREER',
                        'Username'=> $user->getUsername(),
                        'MOT DE PASSE'=>'welcome'
                    ];
                    return new JsonResponse($data, 201);
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
        $test=strtolower($test);
        for ($i=0; $i < $taille; $i++) 
        {
            $retour=1; 
            if (ord($test[$i])==97 || ord($test[$i])==98 || ord($test[$i])==99 || ord($test[$i])==100 || ord($test[$i])==101
            || ord($test[$i])==102 || ord($test[$i])==103 || ord($test[$i])==104 || ord($test[$i])==105 || ord($test[$i])==106
            || ord($test[$i])==107 || ord($test[$i])==108 || ord($test[$i])==109 || ord($test[$i])==110 || ord($test[$i])==111
            || ord($test[$i])==112 || ord($test[$i])==113 || ord($test[$i])==114 || ord($test[$i])==115 || ord($test[$i])==116
            || ord($test[$i])==117 || ord($test[$i])==118 || ord($test[$i])==118 || ord($test[$i])==119 || ord($test[$i])==120
            || ord($test[$i])==121 || ord($test[$i])==122 || $test[$i]=="é" || $test[$i]=="è" || $test[$i]=="ê" 
            || $test[$i]=="à" || $test[$i]=="â" || $test[$i]=="ê" || $test[$i]=="ï" || $test[$i]=="î" || $test[$i]=="ç") {
                $retour=0;
            }
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
}
