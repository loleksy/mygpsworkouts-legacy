<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 11.02.15
 * Time: 21:09
 */

namespace AppBundle\Base;


interface UserPermissionCheckInterface {

    public function isOwnedBy(\AppBundle\Entity\User $user);

}