<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(Request $request, UserAuthenticatorInterface $userAuthenticatorInterface, UserAuthenticator $userAuthenticator, UserRepository $userRepository, UserPasswordHasherInterface $hash): Response
    {
        if ($request->isMethod("POST")) {
            $email = $request->request->get("email");
            $password = $request->request->get("password");

            $user = $userRepository->findOneBy(["email" => $email]);

            if ($user == null || !$user instanceof User) {
                $err = "This email doesn't have an account";
                return $this->render('security/login.html.twig', ['error' => $err]);
            }

            if (!$hash->isPasswordValid($user, $password)) {
                $err = "Incorrect password";
                return $this->render('security/login.html.twig', ['error' => $err]);
            }

            $userAuthenticatorInterface->authenticateUser(
                $user,
                $userAuthenticator,
                $request
            );

            return $this->redirectToRoute("app_home");
        }

        return $this->render('security/login.html.twig', ['error' => null]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
