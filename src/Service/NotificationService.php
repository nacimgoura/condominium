<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function add(string $title, string $content, User $user) {
        $notif = new Notification();
        $notif->setTitle($title);
        $notif->setContent($content);
        $notif->setCreatedAt(new \DateTime());
        $notif->setUser($user);

        $this->em->persist($notif);
        $this->em->flush();
    }
}