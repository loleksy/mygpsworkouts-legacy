<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 15.02.15
 * Time: 14:12
 */

namespace AppBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;


class SportVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::EDIT, self::DELETE);
    }

    protected function getSupportedClasses()
    {
        return array('AppBundle\Entity\Sport');
    }

    protected function isGranted($attribute, $sport, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }
        if ($user->getId() === $sport->getUser()->getId()) {
            return true;
        }
        return false;
    }
}