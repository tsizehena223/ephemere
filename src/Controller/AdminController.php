<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Repository\ArticleRepository;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ArticleRepository $articleRepository, DiscussionRepository $discussionRepository): Response
    {
        if (!$this->isGranted("ROLE_ADMIN")) {
            return $this->redirectToRoute('app_login');
        }

        $articlesToValid = $articleRepository->projectNotVerified();
        $articlesValided = $articleRepository->projectVerified();
        $messages = $discussionRepository->findBy(["recipient" => $this->getUser()]);

        return $this->render("admin/admin.html.twig", ["projetsAValider" => $articlesToValid, "projectsValides" => $articlesValided, "messages" => $messages]);
    }

    #[Route('/admin/approuv/{id}', name: 'app_admin.approuv')]
    public function approuver(ArticleRepository $articleRepository, $id, EntityManagerInterface $em): Response
    {
        $article = $articleRepository->find($id);
        if (isset($article) && $article->getVerifiedAdmin() == 0) {
            $article->setVerifiedAdmin(1);
            $article->setUpdatedAt(new \DateTime());
            $em->persist($article);

            $notifContent = "Félicitation, votre projet a été validé par l'admin! Veuillez signaler par message l'administrateur";

            $notif = new Notification();
            $notif->setRecipient($article->getAuthor());
            $notif->setCreatedAt(new \DateTime());
            $notif->setAuthor("Admin");
            $notif->setContent($notifContent);
            $notif->setSeen(0);
            $notif->setArticleTitle($article->getTitle());
            $em->persist($notif);

            $em->flush();
            $this->addFlash("success", "Projet approuvé avec succès!");
        }
        return $this->redirectToRoute("app_admin");
    }

    #[Route('/admin/remove/{id}', name: 'app_admin.remove')]
    public function remove(ArticleRepository $articleRepository, $id, EntityManagerInterface $em): Response
    {
        $article = $articleRepository->find($id);
        if (isset($article) && $article->getVerifiedAdmin() == 0) {
            $em->remove($article);

            $notifContent = "Malheureusement, votre projet a été refusé par l'admin!";

            $notif = new Notification();
            $notif->setRecipient($article->getAuthor());
            $notif->setCreatedAt(new \DateTime());
            $notif->setAuthor("Admin");
            $notif->setContent($notifContent);
            $notif->setArticleTitle($article->getTitle());
            $notif->setSeen(0);
            $em->persist($notif);

            $em->flush();
            $this->addFlash("error", "Projet supprimé avec succès!");
        }
        return $this->redirectToRoute("app_admin");
    }
}
