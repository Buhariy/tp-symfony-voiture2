<?php

namespace App\Controller\Admin;

use App\Entity\Vehicule;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminVehiculeController extends AbstractController
{
    /**
     * @Route("/admin/createvehicule", name="admin_create_vehicule")
     */
    public function createVehicule(Request $request,EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface){
        $vehicule = new Vehicule();

        $vehiculeForm = $this->createForm(VehiculeType::class, $vehicule);

        $vehiculeForm->handleRequest($request);

        if($vehiculeForm->isSubmitted() && $vehiculeForm->isValid()){
            $vehiculeFile = $vehiculeForm->get('photo')->getData();

            if($vehiculeFile){
                $originaleFileName = pathinfo($vehiculeFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName = $sluggerInterface->slug($originaleFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $vehiculeFile->guessExtension();

                $vehiculeFile->move(
                    $this->getParameter('images_directory'),
                    $newFileName
                );

                $vehicule->setPhoto($newFileName);
            }
            $entityManagerInterface->persist($vehicule);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_vehicule");
        }

        return $this->render("admin/form_vehicule.html.twig",["vehiculeForm" => $vehiculeForm->createView()]);
    }

    /**
     * @Route("/admin/listvehicules", name="admin_list_vehicule")
     */
    public function listVehicules(VehiculeRepository $vehiculeRepository){
        $vehicules = $vehiculeRepository->findAll();

        return $this->render("admin/list_vehicule.html.twig", ["vehicules" => $vehicules]);
    }

    /**
     * @Route("/admin/updatevehicule/{id}", name="admin_update_vehicule")
     */
    public function updateVehicule($id,VehiculeRepository $vehiculeRepository, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface,Request $request){
        $vehicule = $vehiculeRepository->find($id);

        $vehiculeForm = $this->createForm(VehiculeType::class,$vehicule);
        $vehiculeForm->handleRequest($request);

        if($vehiculeForm->isSubmitted() && $vehiculeForm->isValid()){
            $vehiculeFile = $vehiculeForm->get('photo')->getData();

            if($vehiculeFile){
                $originaleFileName = pathinfo($vehiculeFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFileName = $sluggerInterface->slug($originaleFileName);

                $newFileName = $safeFileName . '-' . uniqid() . '.' . $vehiculeFile->guessExtension();

                $vehiculeFile->move(
                    $this->getParameter('images_directory'),
                    $newFileName
                );

                $vehicule->setPhoto($newFileName);
            }

            $entityManagerInterface->persist($vehicule);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_vehicule");
        }

        return $this->render("admin/form_vehicule.html.twig",["vehiculeForm" => $vehiculeForm->createView()]);
    }


    /**
     * @Route("/admin/deletevehicule/{id}", name="admin_delete_vehicule")
     */
    public function deleteVehicule(
        $id,
        EntityManagerInterface $entityManagerInterface,
        VehiculeRepository $vehiculeRepository
    ) {
        $vehicule = $vehiculeRepository->find($id);

        $entityManagerInterface->remove($vehicule);

        $entityManagerInterface->flush();

        return $this->redirectToRoute("admin_list_vehicule");
    }
}
