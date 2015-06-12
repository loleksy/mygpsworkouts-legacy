<?php

namespace AppBundle\Base\WorkoutImport\DataReader;


interface DataReaderInterface {

    /**
     * @return \AppBundle\Base\WorkoutImport\Model\Workout[];
     */
    public function getWorkouts();

}