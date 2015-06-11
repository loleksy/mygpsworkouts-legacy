<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 12.06.15
 * Time: 00:24
 */

namespace AppBundle\Base\WorkoutImport\Validator;


interface ValidatorInterface {

    /**
     * @return string errorMessage
     */
    public function validate();

}