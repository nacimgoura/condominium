<?php

namespace App\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Charge;
use App\Service\EmailService;

class ExpiredCharge extends ContainerAwareCommand
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
            ->setName('charge:list-deadline')

            // the short description shown while running "php bin/console list"
            ->setDescription('Liste les charges restantes à payer dont la date d\'échéance est passée.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Liste les charges restantes à payer dont la date d\'échéance est passée. 
            Possibilité de traiter que les charges de certaines propriétés. 
            Une fois listé, envoie un mail au responsable de la propriété')

            ->addArgument('condominium', InputArgument::OPTIONAL, 'Id de la copropriété ?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $listCharge = $this->getContainer()->get('doctrine')
            ->getRepository(Charge::class)
            ->findAllExpired($input->getArgument('condominium'));

        $output->writeln('Liste des charges avec une date dépassée ('.count($listCharge).')');
        $output->writeln('===========================');
        $output->writeln($listCharge);
        $output->writeln('===========================');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            'Envoyer un mail au responsable ?',
            false,
            '/^(y|j|o)/i'
        );

        $listTitleCharge = [];
        foreach($listCharge as $charge) {
            array_push($listTitleCharge, $charge->getTitle());
        }

        if (count($listCharge) > 0) {
            if ($helper->ask($input, $output, $question)) {
                $this->emailService->sendEmail(
                    $listCharge[0]->getCondominium()->getManager()->getEmail(),
                    'Charges avec date dépassé',
                    'Bonjour, vous avez des charges avec des dates dépassées ('.join($listTitleCharge).') 
                    dans votre copropriété.');
                $output->writeln('Message envoyé avec succès!');
            }
        }
    }
}