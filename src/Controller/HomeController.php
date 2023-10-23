<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(Request $request, UserRepository $userRepository, NotificationRepository $notificationRepository): Response
    {
        if ($this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute("app_admin");
        }

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
                        $users = $userRepository->findByEntreprise($current);
                        break;

                    default:
                        $users = null;
                        break;
                }

                $notifs = $notificationRepository->findNotifsByUser($current);

                return $this->render('home/index.html.twig', [
                    'users' => $users,
                    'notifs' => $notifs
                ]);
            }
        }

        return $this->render('home/index.html.twig');
    }
}
