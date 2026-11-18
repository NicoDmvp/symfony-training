<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Security\User;
use Domain\Collection\UsersCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:jwt', description: 'JWT token generator')]
final class JwtCommand extends Command
{

    public function __construct(
        private readonly \Application\Security\JwtEncoder $jwtEncoder,
        private readonly UsersCollection $usersCollection,
    )
    {
        parent::__construct();
    }

    #[\Override]
    protected function configure()
    {
        $this->addArgument('email', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $user = $this->usersCollection->findOneByEmail($email);

        if (!$user) {
            return Command::FAILURE;
        }

        $jwt = $this->jwtEncoder->encode(new User($user));

        dd($this->jwtEncoder->decode($jwt));

        return Command::SUCCESS;
    }
}
