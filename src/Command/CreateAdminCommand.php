<?php
namespace App\Command;

use App\Entity\Admin;
use App\Repository\AdminRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Create an admin user with username and password',
)]
class CreateAdminCommand extends Command
{
    private AdminRepository $adminRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AdminRepository $adminRepository,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->adminRepository = $adminRepository;
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Admin username')
            ->addArgument('password', InputArgument::REQUIRED, 'Admin password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $username = $input->getArgument('username');
        $plainPassword = $input->getArgument('password');

        if ($this->adminRepository->findOneBy(['username' => $username])) {
            $output->writeln('<error>Admin with this username already exists.</error>');
            return Command::FAILURE;
        }

        $admin = new Admin();
        $admin->setUsername($username);
        $admin->setPassword(
            $this->passwordHasher->hashPassword($admin, $plainPassword)
        );

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $output->writeln('<info>Admin created successfully.</info>');

        return Command::SUCCESS;
    }
}
