<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Entity\Commande;
use App\Entity\Vehicule;
use App\Form\CommandeType;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontCommandeController extends AbstractController
{
    /**
     * @Route("front/create/commande/{id}", name="front_create_commande")
     */
    public function createCommande($id,
     CommandeRepository $commandeRepository,
     Request $request,
     UserRepository $userRepository, EntityManagerInterface $entityManagerInterface,VehiculeRepository $vehiculeRepository)
    {
        $cmd = new Commande();
        $cmdForm = $this->createForm(CommandeType::class, $cmd);
        $vehicule = $entityManagerInterface->getRepository(Vehicule::class);
        //VehiculeRepository::find($id);
        $cmdForm->handleRequest($request);

        if ($cmdForm->isSubmitted() && $cmdForm->isValid()) {
            
            $userEmail = $this->getUser()->getUserIdentifier();
            $userData = $userRepository->findOneBy(['email' => $userEmail]);
            $userData->getId();
            $cmd->setUser($userData);

            $dateEnregistrement = new \DateTime();
            $dateEnregistrement->getTimestamp();
            $cmd->setDateEnregistrement($dateEnregistrement);

            //le calcul du prix total peut se faire ici

            //recuperation des deux dates
            $dateDepart = $cmd->getDateHeureDepart();
            $dateFin = $cmd->getDateHeureFin();

            //calcul de l'intevalle des deux dates
            $intervalle = $dateDepart->diff($dateFin);
            //dd($intervalle);
            $intervalleJours = $intervalle->days;
            //recup du prix journalier du vehicule
            $vehicule = $cmd->getVehicule();
            dd($cmd);
            $prixJournalier  = $vehicule->getPrixJournalier();
            //calcul du prix total
            $prixTotale = $intervalleJours * $prixJournalier;
            $cmd->setPrixTotal($prixTotale);

            $entityManagerInterface->persist($cmd);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("front_list_vehicule");
        }

        return $this->render("front/form_commande.html.twig", ["cmdForm" => $cmdForm->createView()]);
    }
}
