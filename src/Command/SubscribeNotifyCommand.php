<?php

namespace App\Command;

use App\Repository\UserRepository;
use Carbon\Carbon;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SubscribeNotifyCommand extends Command
{
    protected static $defaultName = 'app:subscribe-notify';
    protected static $defaultDescription = 'Add a short description for your command';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * SubscribeNotifyCommand constructor.
     */
    public function __construct(UserRepository $userRepository, MailerInterface $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->userRepository->getUsers() as $user) {
            $daysLeftToExpireSubscribe = $user->daysLeftToExpireSubscribe();

            if ($daysLeftToExpireSubscribe == 1) {
                $email = (new Email())
                    ->from('symfony.diplom@mail.ru')
                    ->to($user->getEmail())
                    ->subject('Истечение срока подписки')
                    ->text("Ваша подписка истекает через 1 день. Не забудьте продлить вашу подписку!")
                ;
                $this->mailer->send($email);
            }
        }

        return Command::SUCCESS;
    }
}
