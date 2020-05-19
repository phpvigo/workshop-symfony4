<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminUserCommand extends Command
{
    protected static $defaultName = 'licensedrawer:create-admin-user';

    private $entityManager;
    private $userPaswordEncoder;

    public function __construct(
        string $name = null,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        parent::__construct($name);
        $this->entityManager = $entityManager;
        $this->userPaswordEncoder = $userPasswordEncoder;
    }

    protected function configure()
    {
        $this
            ->setDescription(
                'Creates admin/admin user with ROLE_ADMIN in database. If admin user exists resets password to "admin".'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $adminUser = $this->entityManager->getRepository(User::class)->findOneByUsername('admin');

        $consoleCreationUserMessage = 'Admin user password and roles restored';

        if (!$adminUser) {
            $adminUser = new User();
            $consoleCreationUserMessage = 'Admin user created';
        }

        $adminPassword = $this->userPaswordEncoder->encodePassword($adminUser, 'admin');

        $adminUser
            ->setUsername('admin')
            ->setPassword($adminPassword)
            ->setRoles([User::ROLE_ADMIN]);

        $this->entityManager->persist($adminUser);
        $this->entityManager->flush();

        $io->success($consoleCreationUserMessage);
        $io->writeln("username: admin");
        $io->writeln("password: admin");
        $io->writeln("roles: [ ROLE_ADMIN ]");
        $io->writeln("");

        return 0;
    }
}
