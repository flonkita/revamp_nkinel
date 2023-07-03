<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddUserCommand extends Command
{
    protected static $defaultName = 'create:admin';
    protected static $defaultDescription = 'Create a user with the "ROLES_ADMIN"';

    private $em;


    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
       $this->em = $entityManagerInterface;
       parent::__construct();
    }

    protected function configure(): void
    {
        
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        

        $user= new User();
        $user->setPassword('test');
        $user->setEmail('test@mail.com');
        // $user->setRoles (array $roles 'ROLE_USER');
        $user->setRoles(['ROLE_ADMIN']);

          
        $this->em->flush();


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}

