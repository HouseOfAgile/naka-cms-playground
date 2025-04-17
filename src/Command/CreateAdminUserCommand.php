<?php

declare (strict_types = 1);

namespace HouseOfAgile\NakaCMSBundle\Command;

use App\Entity\AdminUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Simple command to create an AdminUser from the CLI.
 * 
 * bin/console naka:create-admin-user jc@houseofagile.com somepasswd jc meyo
 */
#[AsCommand(
    name: 'naka:create-admin-user',
    description: 'Create a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;

        // It is important to call the parent constructor AFTER injecting dependencies
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'Email address')
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'Plain password')
            ->addArgument('firstName', InputArgument::OPTIONAL, 'First name', 'Admin')
            ->addArgument('lastName', InputArgument::OPTIONAL, 'Last name', 'User');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Retrieve arguments from the command line
        $email = $input->getArgument('email');
        $plainPassword = $input->getArgument('plainPassword');
        $firstName = $input->getArgument('firstName');
        $lastName = $input->getArgument('lastName');

        // Create and populate the AdminUser
        $user = new AdminUser();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);

        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Grant admin role
        $user->setRoles(['ROLE_ADMIN']);

        // Persist the user in the database
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln(sprintf('AdminUser "%s" created successfully!', $email));

        return Command::SUCCESS;
    }
}
