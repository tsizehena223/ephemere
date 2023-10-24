<?php

namespace App\Controller;

use App\Entity\Discussion;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\DiscussionRepository;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message/send/{id}', name: 'app_message.send', methods: ["POST"])]
    public function send(Request $request, $id, UserRepository $userRepository, EntityManagerInterface $em, DiscussionRepository $discussionRepository): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login", ["error" => "Vous devez vous connecter"]);
        }

        $recipientId = (int)$id;
        $recipientUser = $userRepository->find($recipientId);
        if (!$recipientUser instanceof User) {
            $this->addFlash("error", "Content should not be null");
            return $this->redirectToRoute("app_message.get", ["id" => $recipientId]);
        }

        $content = $request->request->get("message");
        if (!$content || $content == "") {
            $this->addFlash("error", "Content should not be null");
            return $this->redirectToRoute('app_message.get', ["id" => $recipientId]);
        }

        $message = new Message();
        $message->setSender($this->getUser());
        $message->setRecipient($recipientUser);
        $message->setCreatedAt(new \DateTime());
        $message->setContent($content);

        $discussion = $discussionRepository->findOneBy(["sender" => $this->getUser(), "recipient" => $recipientUser]);
        if ($discussion == null) {
            $discussion = new Discussion($this->getUser(), $recipientUser);
            $discussion->setCreatedAt(new \DateTime());
            $discussion->setLastMessage($message->getContent());
            $discussion->setUsers([$this->getUser()->getId(), $recipientId]);
        } else {
            $discussion->setUpdatedAt(new \DateTime());
            $discussion->setLastMessage($message->getContent());
        }

        $message->setDiscussion($discussion);

        $em->persist($message);
        $em->persist($discussion);
        $em->flush();

        return $this->redirectToRoute('app_message.get', ["id" => $recipientId]);
    }

    #[Route(path: "/messages/{id}", name: "app_message.get", methods: ["GET"])]
    public function getMessages(DiscussionRepository $discussionRepository, MessageRepository $messageRepository, $id, UserRepository $userRepository): Response
    {
        if (!$this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("app_login", ["error" => "Vous devez vous connecter"]);
        }

        $recipientId = (int)$id;
        $recipientUser = $userRepository->find($recipientId);

        $discussion = $discussionRepository->findOneBy(["sender" => $this->getUser(), "recipient" => $recipientUser]);

        if ($discussion == null) {
            $discussion = $discussionRepository->findOneBy(["recipient" => $this->getUser(), "sender" => $recipientUser]);
        }

        if ($discussion == null) {
            $discussion = new Discussion($this->getUser(), $recipientUser);
            $discussion->setCreatedAt(new \DateTime());
            $discussion->setUsers([$this->getUser()->getId(), $recipientId]);
        }

        $messages = $messageRepository->findBySenderOrRecipient($this->getUser(), $discussion);

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                "id" => $message["id"],
                "content" => $message["content"],
                "sender" => $message["senderId"],
                "recipient" => $message["recipientId"],
                "createdAt" => $message["createdAt"]->format("h:i:s")
            ];
        }

        return $this->render("message/index.html.twig", ["messages" => $data, "recipient" => $recipientUser]);
    }
}
