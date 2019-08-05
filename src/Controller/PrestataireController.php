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
    /**
     * @Route("/prestataire", name="prestataire")
     */
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
            if ((is_numeric($values->telephone) && $a==0 && $b==0))
            {
                    $user= new User();
                    $user->setPrenom(trim($values->prenom));
                    $user->setNom(trim($values->nom));
                    $user->setCNI($values->CNI);
                    $user->setAdresse($values->adresse);
                    $user->setTelephone(trim($values->telephone));
                    $user->setStatut("ACTIF");
                    $prestataire = $this->getDoctrine()->getRepository(Prestataire::class)->find($values->prestataire);
                    $a=$values->prestataire;
                    $b=rand(20,100);
                    $c=$values->prenom[0].$values->prenom[1];
                    $d=rand(0,20);
                    $e=$values->nom[0].$values->nom[1];
                    $f=$a.$b.$c.$d.$e;
                    $user->setUsername($f);
                    $user->setPrest($prestataire);
                    $user->setPassword($passwordEncoder->encodePassword($user, "welcome"));
                    if ($values->profil==1) {
                        $user->setRoles(['ROLE_ADMIN']);    
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
                        'MOT DE PASSE'=>'welcome',
                        'ROLES'=> $user->getRoles()
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
}
