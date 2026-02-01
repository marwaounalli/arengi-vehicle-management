<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user',
)]
class CreateAdminUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $users,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Admin email', 'admin@test.com')
            ->addOption('password', null, InputOption::VALUE_REQUIRED, 'Admin password (avoid weak passwords)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = (string) $input->getOption('email');
        $password = (string) $input->getOption('password');

        if ($email === '') {
            $io->error('Email is required.');
            return Command::FAILURE;
        }

        if ($password === '') {
            $io->error('Password is required.');
            return Command::FAILURE;
        }

        if ($this->users->findOneBy(['email' => $email])) {
            $io->warning(sprintf('User already exists: %s', $email));
            return Command::SUCCESS;
        }

        $user = (new User())
            ->setEmail($email)
            ->setRoles(['ROLE_ADMIN']);

        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success(sprintf('Admin user created: %s', $email));

        return Command::SUCCESS;
    }
}
