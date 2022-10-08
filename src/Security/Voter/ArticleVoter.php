<?php

namespace App\Security\Voter;

use App\Entity\Article;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ArticleVoter extends Voter
{
    public const CAN_ARTICLE_CREATE = 'CAN_ARTICLE_CREATE';

    /**
     * @var Security
     */
    private $security;


    /**
     * ArticleVoter constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CAN_ARTICLE_CREATE']);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var Article $subject */

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CAN_ARTICLE_CREATE:
                return $user->isAdmin() || $user->getSubscribe()->getCode() == 'pro' || $user->getSubscribe()->getCode() == 'plus' && $user->getArticles()->count() < 10;
                break;
        }

        return false;
    }
}
