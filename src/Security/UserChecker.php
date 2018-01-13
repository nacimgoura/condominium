<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        // user is deleted, show a generic Account Not Found message.
        if ($user->getIsEnabled() == false) {
            throw new UsernameNotFoundException('L\'utilisateur n\'est pas actif');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
    }
}