<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates user with admin role',
    aliases: ['app:add-admin-user'],
    hidden: false
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        parent::__construct();
    }


    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Admin username')
            ->addArgument('password',InputArgument::REQUIRED,'Admin password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username=$input->getArgument('username');
        $password=$input->getArgument('password');

        $user=new User();
        $user->setUsername($username);
        $user->setEmail($username.'@localhost');
        $user->setPassword($this->passwordHasher->hashPassword($user,$password));
        $user->setBalance(1000);
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('Successfully created admin user');

        return Command::SUCCESS;
    }
}
