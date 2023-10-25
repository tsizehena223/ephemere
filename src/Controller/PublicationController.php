<?php

namespace App\Controller;

use App\Entity\Publication;
use App\Repository\PublicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationController extends AbstractController
{
    #[Route('/publication', name: 'app_publication', methods: ["POST"])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login");
        }

        if ($this->getUser()->getProfil() == "entreprise" || $this->getUser()->getProfil() == "admin") {
            if ($request->isMethod("POST")) {
                $pub = new Publication();
                $pub->setContent($request->request->get("content"));
                $pub->setAuthor($this->getUser());
                $pub->setCreatedAt(new \DateTime());

                $image = $request->files->get("image");

                if (!$image instanceof File) {
                    return $this->redirectToRoute("app_login", ["error" => "Image non prise en compte"]);
                }

                $pub->setImageFile($image);
                $em->persist($pub);
                $em->flush();

                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('home/index.html.twig', ["users" => null]);
    }

    #[Route('/publication', name: 'publication.get', methods: ["GET"])]
    public function getPub(PublicationRepository $publicationRepository): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login");
        }

        $pubs = $publicationRepository->findAll();

        return $this->redirectToRoute("app_home", ["pubs" => $pubs]);
    }
}
