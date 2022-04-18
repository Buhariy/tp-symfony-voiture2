<?php

namespace App\Controller\Front;

use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FrontVehiculeController extends AbstractController
{
    /**
     * @Route("/front/listvehicules", name="front_list_vehicule")
     */
    public function listVehicules(VehiculeRepository $vehiculeRepository){
        $vehicules = $vehiculeRepository->findAll();

        return $this->render("front/list_vehicule.html.twig", ["vehicules" => $vehicules]);
    }

    /**
     * @Route("/front/showvehicule/{id}", name="front_show_vehicule")
     */
    public function showVehicule($id,VehiculeRepository $vehiculeRepository){
        $vehicule = $vehiculeRepository->find($id);

        return $this->render("front/show_vehicule.html.twig", ["vehicule" => $vehicule]);
    }
}
