<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Doctrine\ORM\Query\AST\Functions\CurrentDateFunction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FrontUserController extends AbstractController{

    /**
     * @Route("/front/user/insert", name="front_create_user")
     */
    public function createUser(Request $request,EntityManagerInterface $entityManagerInterface,UserPasswordHasherInterface $userPasswordHasherInterface){
        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid()){
            $user->setRoles(["ROLE_USER"]);
            $date = new \DateTime();
            $date->getTimestamp();
            $user->setDateEnregistrement($date);

            // On récupère le password entré dans le formulaire.
            $plainpassword = $userForm->get('password')->getData();

            // Hashage du password
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainpassword);
            $user->setPassword($hashedPassword);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('front/form_user.html.twig', ['userForm' => $userForm->createView()]);
    }
}