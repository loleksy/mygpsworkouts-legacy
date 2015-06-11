<?php

namespace AppBundle\Base\WorkoutImport\Validator;

use AppBundle\Base\WorkoutImport\Model\Workout;

class WorkoutValidator implements ValidatorInterface {

    /**
     * @var Workout
     */
    protected $workout;

    public function __construct(Workout $workout){
        $this->workout = $workout;
    }

    public function validate(){
        if(count($this->workout->getTrackPoints())<2){
            return 'No trackpoints';
        }
        if(!$this->workout->getStartDateTime()){
            return 'No startDatetime';
        }
        foreach($this->workout->getTrackPoints() as $trackPoint){
            $trackPointValidator = new TrackPointValidator($trackPoint);
            $trackPointError = $trackPointValidator->validate();
            if($trackPointError){
                return $trackPointError;
            }
        }
        return null;
    }



}