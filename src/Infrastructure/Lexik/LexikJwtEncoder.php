<?php

declare(strict_types=1);

namespace Infrastructure\Lexik;

use Application\Security\JwtEncoder;
use Application\Security\User;
use Infrastructure\Doctrine\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

final readonly class LexikJwtEncoder implements JwtEncoder
{

    public function __construct(private JWTTokenManagerInterface $tokenManager, private UserRepository $userRepository)
    {
    }

    #[Override]
    public function encode(User $user): string
    {
        return $this->tokenManager->createFromPayload($user);
    }

    #[Override]
    public function decode(string $jwt): User
    {
        $data = $this->tokenManager->parse($jwt);

        if (!\array_key_exists('username', $data)) {
            throw new \RuntimeException('Invalid JWT');
        }

        $user = $this->userRepository->findOneByEmail($data['username']);

        if (null === $user) {
            throw new UserNotFoundException('Invalid JWT token');
        }

        return new User($user);
    }
}

