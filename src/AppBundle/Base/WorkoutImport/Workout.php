<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 01.02.15
 * Time: 18:19
 */

namespace AppBundle\Base\WorkoutImport;


use AppBundle\Entity\User;
use Location\Coordinate;
use Location\Distance\Haversine;

class Workout {

    /**
     * @var string|null
     */
    protected $errorMessage = null;

    /**
     * @var string
     */
    protected $sport;

    /**
     * @var \DateTime|null
     */
    protected $startDateTime;

    /**
     * @var int
     */
    protected $distanceMeters;

    /**
     * @var int
     */
    protected $totalTimeSeconds;

    /**
     * @var int
     */
    protected $averageHeartRateBpm;

    /**
     * @var int
     */
    protected $maximumHeartRateBpm;

    /**
     * @var Trackpoint[]
     */
    protected $trackPoints;



    /**
     * @var int
     */
    protected $calories;



    public function __construct(){
        $this->sport = 'unknown';
        $this->trackPoints = array();
    }

    /**
     * @return null|string
     */
    public function getErrorMessage(){
        return $this->errorMessage;
    }

    /**
     * @return string
     */
    public function getSport()
    {
        return $this->sport?$this->sport:'unknown';
    }

    /**
     * @param string $sport
     */
    public function setSport($sport)
    {
        $this->sport = $sport;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * @param \DateTime|null $startDateTime
     */
    public function setStartDateTime($startDateTime)
    {
        $this->startDateTime = $startDateTime;
    }

    /**
     * @return int
     */
    public function getDistanceMeters()
    {
        if($this->distanceMeters){
            return $this->distanceMeters;
        }
        else{
            $totalPoints = count($this->trackPoints);
            $calculator = new Haversine();
            $distance = 0;
            for($i=1; $i< $totalPoints; $i++){
                $coord1 = new Coordinate((float)$this->trackPoints[$i-1]->getLat(),(float)$this->trackPoints[$i-1]->getLng());
                $coord2 = new Coordinate((float)$this->trackPoints[$i]->getLat(),(float)$this->trackPoints[$i]->getLng());
                $distance+=$calculator->getDistance($coord1, $coord2);
            }
            return $distance;
        }

    }

    /**
     * @param int $distanceMeters
     */
    public function setDistanceMeters($distanceMeters)
    {
        $this->distanceMeters = $distanceMeters;
    }

    /**
     * @return int
     */
    public function getCalories()
    {
        return $this->calories;
    }

    /**
     * @param int $calories
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;
    }

    /**
     * @return int
     */
    public function getTotalTimeSeconds()
    {
        if($this->totalTimeSeconds){
            return $this->totalTimeSeconds;
        }
        else{
            $totalPoints = count($this->trackPoints);
            $result = 0;
            for($i=1; $i<$totalPoints; $i++){
                $diff = $this->trackPoints[$i]->getDateTime()->getTimestamp() - $this->trackPoints[$i-1]->getDateTime()->getTimestamp();
                $result+=$diff;
            }
            return $result;
        }
    }

    /**
     * @param int $totalTimeSeconds
     */
    public function setTotalTimeSeconds($totalTimeSeconds)
    {
        $this->totalTimeSeconds = $totalTimeSeconds;
    }

    /**
     * @return int
     */
    public function getAverageHeartRateBpm()
    {
        if($this->averageHeartRateBpm){
            return $this->averageHeartRateBpm;
        }
        $hearthRateValues = $this->getHearthRateValues();
        if($hearthRateValues){
            return array_sum($hearthRateValues) / count($hearthRateValues);
        }
        return null;
    }

    /**
     * @param int $averageHeartRateBpm
     */
    public function setAverageHeartRateBpm($averageHeartRateBpm)
    {
        $this->averageHeartRateBpm = $averageHeartRateBpm;
    }

    /**
     * @return int
     */
    public function getMaximumHeartRateBpm()
    {
        if($this->maximumHeartRateBpm){
            return $this->maximumHeartRateBpm;
        }
        $hearthRateValues = $this->getHearthRateValues();
        if($hearthRateValues){
            return max($hearthRateValues);
        }
        return null;
    }

    /**
     * @param int $maximumHeartRateBpm
     */
    public function setMaximumHeartRateBpm($maximumHeartRateBpm)
    {
        $this->maximumHeartRateBpm = $maximumHeartRateBpm;
    }

    /**
     * @return Trackpoint[]
     */
    public function getTrackPoints(){
        return $this->trackPoints;
    }

    public function addTrackPoint($trackPoint){
        $this->trackPoints[] = $trackPoint;
    }

    /**
     * Is workout valid?
     * @return bool
     */
    public function isValid(){
        if(count($this->getTrackPoints())<2){
            $this->errorMessage = 'No trackpoints';
            return false; //my GPS! workouts ;)
        }
        if(!$this->getStartDateTime()){
            $this->errorMessage = 'No startDatetime';
            return false;
        }
        foreach($this->getTrackPoints() as $trackPoint){
            if(!$trackPoint->isValid()){
                $this->errorMessage = 'invalid trackpoint detected';
                return false;
            }
        }
        return true;

    }

    public function getHearthRateValues(){
        $values = array();
        foreach($this->trackPoints as $trackPoint){
            if($trackPoint->getHearthRateBpm()){
                $values[] = $trackPoint->getHearthRateBpm();
            }
        }
        return $values;
    }

    /**
     * @return \Workout
     */
//    public function saveInDb(\User $user){
//        $model = new \Workout();
//        \DB::transaction(function() use ($model, $user)
//        {
//            $model->user()->associate($user);
//            $sport = $user->sports()->where('user_id', $user->id)->where('name', $this->getSport())->first();
//            if(!$sport){
//                $sport = new \Sport();
//                $sport->name = $this->getSport();
//                $sport->display_name = $this->getSport();
//                $sport->color = '#'.dechex(rand(0x000000, 0xFFFFFF));
//                $sport->user()->associate($user);
//                $sport->save();
//            }
//            $model->sport()->associate($sport);
//            $model->start_datetime = $this->getStartDateTime()->format('Y-m-d H:i:s');
//            $model->total_time_seconds  = $this->getTotalTimeSeconds();
//            $model->distance_meters = $this->getDistanceMeters();
//            $model->calories = $this->getCalories();
//            $model->average_hearth_rate_bpm = $this->getAverageHeartRateBpm();
//            $model->maximum_hearth_rate_bpm = $this->getMaximumHeartRateBpm();
//            $model->save();
//            $index = 0;
//            foreach($this->getTrackPoints() as $trackpoint){
//                $trackpointModel = new \Trackpoint();
//                $trackpointModel->workout()->associate($model);
//                $trackpointModel->datetime = $trackpoint->getDatetime()->format('Y-m-d H:i:s');
//                $trackpointModel->index = $index;
//                $trackpointModel->lat = $trackpoint->getLat();
//                $trackpointModel->lng = $trackpoint->getLng();
//                $trackpointModel->altitude_meters = $trackpoint->getAltitudeMeters();
//                $trackpointModel->heart_rate_bpm = $trackpoint->getHearthRateBpm();
//                $index+=1;
//                $trackpointModel->save();
//            }
//        });
//
//        return $model;
//    }

}
