<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

class EmailService
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->templating = $container->get('twig');
    }

    public function sendEmail($recipient, $title, $message, $listNotif=null, $token=null) {
        $email = (new \Swift_Message($title))
            ->setFrom('nacim.goura@gmail.com')
            ->setTo($recipient)
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig', [
                        'message' => $message,
                        'listNotif' => $listNotif,
                        'token' => $token
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($email);
    }
}