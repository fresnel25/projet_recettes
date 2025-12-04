<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendNewsletterCommand extends Command
{
    protected static $defaultName = 'app:send-newsletter';
    protected static $defaultDescription = 'Envoie la newsletter à tous les utilisateurs';

    private $userRepository;
    private $mailer;

    public function __construct(UserRepository $userRepository, MailerInterface $mailer)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {
            $email = (new Email())
                ->from('newsletter@recettes.local')
                ->to($user->getEmail())
                ->subject('Notre Newsletter')
                ->text('Bonjour ' . $user->getNom() . ", voici notre dernière newsletter !")
                ->html('<p>Bonjour ' . $user->getNom() . ',</p><p>Voici notre dernière newsletter !</p>');

            $this->mailer->send($email);
            $output->writeln('Email envoyé à ' . $user->getEmail());
        }

        $output->writeln('Newsletter envoyée à tous les utilisateurs.');
        return Command::SUCCESS;
    }
}
