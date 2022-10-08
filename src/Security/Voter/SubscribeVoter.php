<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscribeVoter extends Voter
{
    public const PLUS = 'PLUS';
    public const PRO = 'PRO';

    protected function supports(string $attribute, $subject): bool
    {
        return true;
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::PLUS, self::PRO])
            && $subject instanceof \App\Entity\Subscribe;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::PLUS:
                return $user->isAdmin() || $user->getSubscribe()->getCode() == mb_strtolower(self::PLUS) || $user->getSubscribe()->getCode() == mb_strtolower(self::PRO);
                break;
            case self::PRO:
                return $user->isAdmin() || $user->getSubscribe()->getCode() == mb_strtolower(self::PRO);
                break;
        }

        return false;
    }
}
