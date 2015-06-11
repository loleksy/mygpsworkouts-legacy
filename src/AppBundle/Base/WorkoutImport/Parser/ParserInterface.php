<?php

namespace AppBundle\Base\WorkoutImport\Parser;


interface ParserInterface {

    /**
     * @return \AppBundle\Base\WorkoutImport\Model\Workout[];
     */
    public function parseWorkouts();

}