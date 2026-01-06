<?php

namespace App\Security\Voter;

use App\Entity\Contact\Contact;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class ContactVoter extends Voter
{
    public const EDIT = 'CONTACT_EDIT';
    public const VIEW = 'CONTACT_VIEW';
    public const DELETE = 'CONTACT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Contact;
    }

    protected function voteOnAttribute(string $attribute, mixed $contact, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if(in_array('ROLE_ADMIN', $user->getRoles(), true))
        {
            return true;
        }

        /*if($contact->getWorkspace()->getId() !== $user->getCurrentWorksapce()->getId())
        {
            return false;
        }*/

        return $contact->getOwner()->getId() === $user->getId();
    }
}
