<?php

declare(strict_types=1);

namespace Infrastructure\Symfony\Security;

use Application\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class DinosaurVoter extends Voter
{
    public const string ATTRIBUTE = 'VIEW_DINOSAURS';

    public function __construct(
        private readonly AccessDecisionManagerInterface $accessDecisionManager,
    )
    {
    }


    #[\Override]
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::ATTRIBUTE;
    }

    #[\Override]
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        dump($subject);
        if ($this->accessDecisionManager->decide($token, ['ROLE_ADMIN'])) {
            return true;
        }
        return false;
    }
}
