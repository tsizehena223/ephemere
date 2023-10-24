<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    #[Route('/users/search', name: 'app_users.search', methods: ["POST"])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login");
        }

        $name = $request->request->get('name');
        if ($name == null) {
            return $this->render('home/index.html.twig');
        } else {
            $profil = $this->getUser()->getProfil();

            if ($profil == "etudiant" || $profil == "professeur") {
                $users = $userRepository->findByNameAndProfEtud($this->getUser()->getId(), $name);
            } elseif ($profil == "entreprise") {
                $users = $userRepository->findByNameAndEntreprise($this->getUser()->getId(), $name);
            } elseif ($profil == "investisseur") {
                $users = $userRepository->findByNameAndInvestisseur($this->getUser()->getId(), $name);
            } else {
                $users = [];
            }

            return $this->render('home/index.html.twig', ["usersSearch" => $users]);
        }
    }
}
