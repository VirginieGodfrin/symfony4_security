<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\Article;

class ArticleVoter extends Voter
{
    // injection d'indépendence security service
    // We used it because it gives us access to the current User object and the isGranted() method!
    // The Security service is the key to get the User or check if the user has access for some permission attribute.
    private $security;
    public function __construct(Security $security) {
        $this->security = $security; 
    }
    // whenever anybody in the system calls isGranted() with any permission attribute string, 
    // the supports() method on your voter will be called:
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        // The $attribute argument will be the string passed to isGranted() and $subject is the second argument - 
        // the Article object for us. 
        return in_array($attribute, ['MANAGE'])
            && $subject instanceof Article;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // The token is a lower-level object that you don't see too often. 
        // But, you can use it to get access to the User object:
        // 
        /** @var Article $subject */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        // This is where our logic goes to determine access.
        // If we return true, access will be granted. If we return false, access will be denied.
        switch ($attribute) {
            case 'MANAGE':
                // The current user is the author and so access should be granted.
                if ($subject->getAuthor() == $user) { 
                    return true;
                }
                // security->isGranted()
                if ($this->security->isGranted('ROLE_ADMIN_ARTICLE')) { 
                    return true;
                }
                break;
        }

        return false;
    }
}
