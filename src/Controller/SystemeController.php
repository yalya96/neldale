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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Depot;
use Doctrine\ORM\EntityNotFoundException;

class SystemeController extends AbstractController
{
    /**
     * @Route("/", name="systeme")
     */
    public function index()
    {
        return $this->render('systeme/index.html.twig', [
            'controller_name' => 'SystemeController',
        ]);
    }
    /**
     * @Route("/ajoutsys")
     * @IsGranted("ROLE_SUPERADMIN")
     */
    public function addsys(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $a=$this->chiffre($values->prenom);
        $b=$this->chiffre($values->nom);
        if(!empty($values->prenom) && !empty($values->nom) && !empty($values->username) &&
            $a==0 && $b==0 && ($values->profil==2 || $values->profil==1) && is_numeric($values->telephone) && (strlen($values->telephone)==9) )
        {
                    $user= new User();
                    $user->setPrenom(trim($values->prenom));
                    $user->setNom(trim($values->nom));
                    $user->setTelephone(trim($values->telephone));
                    $user->setUsername(trim($values->username));
                    $user->setPassword($passwordEncoder->encodePassword($user, "welcome"));
                    if ($values->profil==1) {
                        $user->setRoles(['ROLE_ADMIN']);    
                    }
                    if ($values->profil==2) {
                        $user->setRoles(['ROLE_CAISSIER']);   
                    }
                    $user->setStatut("ACTIF"); 
                    $entityManager->persist($user);
                    $entityManager->flush();
                    $data=[
                        'Message'=>'UTILISATEUR CREER',
                        'Username'=> $user->getUsername(),
                        'MOT DE PASSE'=>'welcome'
                    ];
                    return new JsonResponse($data, 500); 
        }
            else {
                $data = [
                    'status' => 500,
                    'message' => 'UTILISATEUR NONCREER'
                ];
                return new JsonResponse($data, 500);
            }
    }
    /**
     * @Route("/ajoutprest")
     * @Security("has_role('ROLE_SUPERADMIN')")
     */
    public function addprest(Request $request, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());
        $a=$this->chiffre($values->NomEntreprise);
        $b=$this->chiffre($values->prenom);
        $c=$this->chiffre($values->nom);
        if(!empty($values->NomEntreprise) && !empty($values->ninea) && !empty($values->AdresseEntreprise) && !empty($values->TelephoneEntreprise) && !empty($values->NumeroRegistre)
            && $a==0 && $b==0 && $c==0 && is_numeric($values->TelephoneEntreprise) && is_numeric($values->NumeroRegistre)
            && is_numeric($values->telephone) && (strlen($values->telephone)==9) && (strlen($values->TelephoneEntreprise)==9) && (strlen($values->CNI)==16)
            && !empty($values->prenom) && !empty($values->nom))
        {
            $prestataire= new Prestataire();
            $prestataire->setNomEntreprise($values->NomEntreprise);
            $prestataire->setNinea($values->ninea);
            $prestataire->setAdresse($values->AdresseEntreprise);
            $prestataire->setTelephone($values->TelephoneEntreprise);
            $prestataire->setNumeroDeRegistre($values->NumeroRegistre);
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
            $e=strtoupper($values->NomEntreprise[0]);
            $num=$e.$num;
            $compte->setNumeroDeCompte($num);
            $compte->setPrest($prestataire);
            $user->setCompte($compte);
            try {
                $entityManager->persist($user);
                $entityManager->persist($prestataire);
                $entityManager->persist($compte);
                $entityManager->flush();
                $data = [
                    'status' => 201,
                    'message' => 'UTILISATEUR CREER'
                ];
                return new JsonResponse($data, 500);
            } catch (EntityNotFoundException $e ) {
                $data = [
                    'status' => 201,
                    'message' => 'UTILISATEUR NON CREER'
                ];
                return new JsonResponse($data, 500);
            }
            $entityManager->persist($user);
            $entityManager->persist($prestataire);
            $entityManager->persist($compte);
            $entityManager->flush();
            $data = [
                'status' => 201,
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
    /**
     * @Route("/depot", name="depot", methods={"POST"})
     */
    public function yaya(Request $request, EntityManagerInterface $entityManager)
    {
        $values = json_decode($request->getContent());

            $a1=$this->getUser()->getId();        
            $a=$this->getDoctrine()->getRepository(Compte::class)->findOneBy(["NumeroDeCompte" => $values->NumeroDeCompte]);
            if ($values->montant>=75000 && $a) 
            {
            $b=$this->getDoctrine()->getRepository(User::class)->find($a1);
            $a2=$a->getId();
            $c=$this->getDoctrine()->getRepository(Compte::class)->find($a2);
            $a3=$a->getMontant();
            $depot= new Depot();
            $depot->setCaissier($b);
            $depot->setCompte($c);
            $depot->setDateDeDepot( new \DateTime());
            $depot->setSoldeInitial($a3);
            $depot->setMontant($values->montant);
            $entityManager = $this->getDoctrine()->getManager();
            $jour = $entityManager->getRepository(Compte::class)->find($c);
            $jour->setMontant($a3+$values->montant);
            $entityManager->persist($depot);
            $entityManager->flush();
            $data = [
                'status' => 201,
                'message' => 'merci'
            ];
            return new JsonResponse($data, 500);    
        }
        else {
            $data = [
                'status' => 500,
                'message' => 'DARABAKHOUL'
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
