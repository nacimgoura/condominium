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

    public function sendEmail($recipient, $title, $message) {
        $message = (new \Swift_Message($title))
            ->setFrom('send@example.com')
            ->setTo('nacim.goura@gmail.com')
            ->setBody(
                $this->templating->render(
                    'emails/notification.html.twig', [
                        'message' => $message
                    ]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}