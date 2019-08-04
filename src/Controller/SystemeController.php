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
       /**
     * @Route("/ajoutprestataire")
     */
    public function addprest(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        if(isset($values->NomEntreprise,$values->ninea,$values->AdresseEntreprise,$values->TelephoneEntreprise,$values->NumeroRegistre))
        {
            $prestataire= new Prestataire();
            $prestataire->setNomEntreprise($values->NomEntreprise);
            $prestataire->setNinea($values->ninea);
            $prestataire->setAdresse($values->AdresseEntreprise);
            $prestataire->setTelephone($values->TelephoneEntreprise);
            $prestataire->setNumeroDeRegistre($values->NumeroRegistre);
            $prestataire->setMail($values->mail);
            $prestataire->setStatut("ACTIF");
            
            $user= new User();
            $user->setPrenom($values->prenom);
            $user->setCNI($values->CNI);
            $user->setNom($values->nom);
            $user->setTelephone($values->telephone);
            $a=$prestataire->getId();
            $b=rand(20,100);
            $c=$values->prenom[0].$values->prenom[1];
            $d=rand(0,20);
            $e=$values->NomEntreprise[0].$values->NomEntreprise[1];
            $f=$a.$b.$c.$d.$e;
            $user->setUsername($f);
            $user->setPassword($passwordEncoder->encodePassword($user, "welcome"));
            $user->setRoles(['ROLE_PRESTATAIRE']);
            $user->setAdresse($values->adresse);
            $user->setPrest($prestataire);

            $compte= new Compte();
            $compte->setMontant(0);
            $num=$this->numerocompte();
            $compte->setNumeroDeCompte($num);
            $compte->setPrest($prestataire);
            $user->setCompte($compte);
            $entityManager->persist($user);
            $entityManager->persist($prestataire);
            $entityManager->persist($compte);
            $entityManager->flush();
            $data = [
                'status' => 500,
                'message' => 'UTILISATEUR CREER'
            ];
            return new JsonResponse($data, 500);
        }
        $data = [
            'status' => 500,
            'message' => 'DARA BAKHOUL'
        ];
        return new JsonResponse($data, 500);
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
