<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddInformationController extends AbstractController
{
    #[Route('/add/information', name: 'app_add_information')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->render('add_information/index.html.twig', ["error" => "Error"]);
        }
        if ($request->isMethod("POST")) {
            $cin = $request->request->get("cin");
            $profil = $request->request->get("profil");

            if (strlen($cin) < 12) {
                $err = "CIN should have 12 numbers";
                return $this->render('add_information/index.html.twig', ["error" => $err]);
            }

            $user = $this->getUser();
            if (!$user || !$user instanceof User) {
                $err = "Error, please try again";
                return $this->render('add_information/index.html.twig', ["error" => $err]);
            }

            $user->setProfil($profil);
            $user->setCin($cin);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("app_home");
        }
        return $this->render('add_information/index.html.twig', ['error' => null]);
    }
}
