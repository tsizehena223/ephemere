<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegisterController extends AbstractController
{
    public function __construct(private UserPasswordHasherInterface $hash)
    {
    }

    #[Route('/api/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $em, UserAuthenticatorInterface $userAuthenticatorInterface, UserAuthenticator $userAuthenticator, UserRepository $userRepository, UserPasswordHasherInterface $hash): Response
    {
        if ($request->isMethod("POST")) {
            $name = $request->request->get("name");
            $email = $request->request->get("email");
            $password = $request->request->get("password");

            $oldUser = $userRepository->findOneBy(["email" => $email]);
            if ($oldUser) {
                $err = "Email already token";
                return $this->render("security/login.html.twig", ['error' => $err]);
            }

            if (strlen($password) < 4) {
                $err = "Password should contain 4 characters at least";
                return $this->render("security/login.html.twig", ['error' => $err]);
            }

            $user = new User();
            $password = $hash->hashPassword($user, $password);

            $user->setName($name);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRoles(["ROLE_USER"]);

            $em->persist($user);
            $em->flush();

            $this->addFlash("success", "Account created successfully");
            $userAuthenticatorInterface->authenticateUser(
                $user,
                $userAuthenticator,
                $request
            );

            return $this->redirectToRoute("app_add_information");
        }
        return $this->render("security/login.html.twig");
    }

    #[Route('/profil', name: 'app_profil', methods: ["POST"])]
    public function profil(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod("POST")) {
            $user = $this->getUser();

            if ($user instanceof User) {
                $user->setName($request->request->get('name'));
                $user->setEmail($request->request->get('email'));

                $pdpFile = $request->files->get('pdpFile');

                $user->setImageFile($pdpFile);

                $em->persist($user);
                $em->flush();
            }

            return $this->redirectToRoute("app_home");
        }
        return $this->render("profil/profil.html.twig");
    }
}
