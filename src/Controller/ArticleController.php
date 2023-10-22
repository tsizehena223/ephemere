<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/create', name: 'article.create', methods: ["POST"])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute("app_login");
        }

        if ($request->isMethod("POST")) {
            $author = $this->getUser();
            if (!$author) {
                return $this->render('home/index.html.twig', ["error" => "Not connected"]);
            }
            $situationTravail = $request->request->get('travail');
            $domaineActivite = $request->request->get('activite');

            $projectFile = $request->files->get('projectFile');

            $article = new Article();
            $article->setDomaine($domaineActivite);
            $article->setSituationTravail($situationTravail);
            $article->setAuthor($author);
            $article->setVerifiedAdmin(0);

            $article->setProjectFile($projectFile);

            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('app_home');
        }
        return $this->render('home/index.html.twig', ["users" => null]);
    }
}
