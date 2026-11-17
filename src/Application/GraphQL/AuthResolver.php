<?php

namespace Application\GraphQL;

use Application\Security\JwtEncoder;
use Application\Security\User;
use Domain\Collection\UsersCollection;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class AuthResolver
{
    public function __construct(
        private UsersCollection $users,
        private JwtEncoder $jwtEncoder,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function __invoke(string $email, string $password): string
    {
        $user = $this->users->findOneByEmail($email);

        if (!$user) {
            throw new \Exception('User not found');
        }
        $userSecurity = new User($user);

        if (! $this->passwordHasher->isPasswordValid($userSecurity, $password)) {
            throw new \Exception('Wrong password');
        }
        return $this->jwtEncoder->encode($userSecurity);
    }
}
