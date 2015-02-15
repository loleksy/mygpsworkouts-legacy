<?php


namespace AppBundle\Base\WorkoutImport\Tcx;


use AppBundle\Base\WorkoutImport\TrackPoint;
use AppBundle\Base\WorkoutImport\Workout;
use AppBundle\Base\WorkoutImport\WorkoutImportInterface;

class Parser implements WorkoutImportInterface {

    /**
     * @var array
     */
    protected $workouts;
    /**
     * @var \SimpleXMLElement
     */
    protected $xmlobj;

    public function __construct(\SimpleXMLElement $xmlObject){
        $this->xmlObj = $xmlObject;
        $this->workouts = array();
    }

    public function parse(){
        $this->workouts = array();
        if(isset($this->xmlObj->Activities) && isset($this->xmlObj->Activities->Activity)){
            foreach($this->xmlObj->Activities->Activity as $activity){
                $this->workouts[] = $this->parseActivity($activity);
            }
        }

    }

    /**
     * @return \AppBundle\Base\WorkoutImport\Workout[];
     */
    public function getWorkouts(){
        return $this->workouts;
    }

    protected function parseActivity(\SimpleXMLElement $activity){
        $workout = new Workout();
        if(isset($activity->attributes()->Sport)){
            $workout->setSport((string)$activity->attributes()->Sport);
        }
        $workout->setCalories(0);
        $workout->setDistanceMeters(0);
        $workout->setTotalTimeSeconds(0);
        if(isset($activity->Lap)){
            foreach($activity->Lap as $lap){
                $this->parseLap($lap, $workout);
            }
        }
        return $workout;
    }

    protected function parseLap(\SimpleXMLElement $lap, Workout $workout){
        if(isset($lap->attributes()->StartTime) && !$workout->getStartDateTime()){
            $workout->setStartDateTime(new \DateTime((string)$lap->attributes()->StartTime));
        }
        if(isset($lap->DistanceMeters)){
            $currentMeters = $workout->getDistanceMeters()?$workout->getDistanceMeters():0;
            $workout->setDistanceMeters($currentMeters + (int)$lap->DistanceMeters);
        }
        if(isset($lap->TotalTimeSeconds)){
            $currentTotalTime = $workout->getTotalTimeSeconds()?$workout->getTotalTimeSeconds():0;
            $workout->setTotalTimeSeconds($currentTotalTime + (int)$lap->TotalTimeSeconds);
        }
        if(isset($lap->Calories)){
            $currentCalories = $workout->getCalories()?$workout->getCalories():0;
            $workout->setCalories($currentCalories + (int)$lap->Calories);
        }
        if(isset($lap->AverageHeartRateBpm) && isset($lap->AverageHeartRateBpm->Value)){
            $currentRate = $workout->getAverageHeartRateBpm()?$workout->getAverageHeartRateBpm():0;
            $workout->setAverageHeartRateBpm($currentRate + (int)$lap->AverageHeartRateBpm->Value);
        }
        if(isset($lap->MaximumHeartRateBpm) && isset($lap->MaximumHeartRateBpm->Value)) {
            $currentRate = $workout->getMaximumHeartRateBpm() ? $workout->getMaximumHeartRateBpm() : 0;
            $workout->setMaximumHeartRateBpm($currentRate + (int)$lap->MaximumHeartRateBpm->Value);
        }
        if($lap->Track){
            $trackPoints = $lap->Track->Trackpoint;
        }
        else{
            $trackPoints = $lap->Trackpoint;
        }
        foreach($trackPoints as $trackPoint){
            $parsedTrackPoint = $this->parseTrackPoint($trackPoint);
            if($parsedTrackPoint){
                $workout->addTrackPoint($parsedTrackPoint);
            }
        }

    }

    protected function parseTrackPoint(\SimpleXMLElement $trackPoint){
        if(
            !isset($trackPoint->Position)
            || !isset($trackPoint->Position->LatitudeDegrees)
            || !isset($trackPoint->Position->LongitudeDegrees)
            || !isset($trackPoint->Time)
        ){
            return;
        }
        $obj = new TrackPoint();
        $obj->setLat((string)$trackPoint->Position->LatitudeDegrees);
        $obj->setLng((string)$trackPoint->Position->LongitudeDegrees);
        $obj->setDateTime(new \DateTime((string)$trackPoint->Time));
        if(isset($trackPoint->AltitudeMeters)){
            $obj->setAltitudeMeters((int)$trackPoint->AltitudeMeters);
        }
        if(isset($trackPoint->HeartRateBpm) && isset($trackPoint->HeartRateBpm->Value)){
            $obj->setHearthRateBpm((int)$trackPoint->HeartRateBpm->Value);
        }
        return $obj;
    }

}