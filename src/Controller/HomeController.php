<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        if ($request->isMethod("POST")) {
            return $this->render('home/index.html.twig', ["users" => null]);
        }

        if ($this->getUser() != null) {
            $current = $this->getUser();
            if ($current instanceof User) {
                switch ($current->getProfil()) {
                    case 'etudiant':
                        $users = $userRepository->findByProfEtud($current->getId());
                        break;

                    case 'professeur':
                        $users = $userRepository->findByProfEtud($current->getId());
                        break;

                    case 'investisseur':
                        $users = $userRepository->findByInvestisseur($current->getId());
                        break;

                    case 'entreprise':
                        $users = $userRepository->findByEntreprise($current->getId());
                        break;

                    default:
                        $users = null;
                        break;
                }

                return $this->render('home/index.html.twig', [
                    'users' => $users
                ]);
            }
        }

        return $this->render('home/index.html.twig');
    }
}
