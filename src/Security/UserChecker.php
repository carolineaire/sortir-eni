<?php
namespace App\Security;

use App\Entity\Participants;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Participants) {
            return;
        }

        if (!$user->isActif()) {
            throw new CustomUserMessageAuthenticationException(
                'Votre compte est désactivé. Contactez un administrateur.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }
}
