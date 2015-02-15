<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 01.02.15
 * Time: 17:48
 */

namespace AppBundle\Base\WorkoutImport;

interface WorkoutImportInterface {

    /**
     * @return \AppBundle\Base\WorkoutImport\Workout[];
     */
    public function getWorkouts();
}