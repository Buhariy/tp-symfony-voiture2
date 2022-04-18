<?php

namespace App\Controller\Admin;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommandeController extends AbstractController
{
   /**
    * @Route("admin/listcommande", name="admin_list_commande")
    */
    public function listCommande(CommandeRepository $commandeRepository){
        $commandes = $commandeRepository->findAll();
        //dd($commandes);

        return $this->render("admin/list_commande.html.twig", ["commandes"=>$commandes]);
    }

    
}
