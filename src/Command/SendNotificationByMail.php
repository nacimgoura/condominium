<?php

namespace App\Command;

use App\Entity\User;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendNotificationByMail extends ContainerAwareCommand
{
    private $emailService;

    public function __construct(?string $name = null, EmailService $emailService)
    {
        $this->emailService = $emailService;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('notification:send-mail')

            // the short description shown while running "php bin/console list"
            ->setDescription('Envoie les notifications de la journée aux personnes.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');

        $listUser = $doctrine->getRepository(User::class)->findAll();

        foreach($listUser as $user) {
            if (count($user->getNotification()) > 0) {
                $this->emailService->sendEmail(
                    $user->getEmail(),
                    'Notification de la journée',
                    'Bonjour, vous avez des nouvelles notifications dans votre copropriété.',
                    $user->getNotification()
                );
                $output->writeln('Message envoyé avec succès à '.$user->getEmail());
            }
        }

    }

}