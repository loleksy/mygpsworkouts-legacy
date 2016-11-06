<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 01.02.15
 * Time: 18:19
 */

namespace AppBundle\Base\WorkoutImport\Model;


use Location\Coordinate;
use Location\Distance\Haversine;

class Workout {

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
        if ($this->startDateTime) {
            return $this->startDateTime;
        }

        if ($this->trackPoints) {
            return $this->trackPoints[0]->getDatetime();
        }

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
        $heartRateValues = $this->getHeartRateValues();
        if($heartRateValues){
            return array_sum($heartRateValues) / count($heartRateValues);
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
        $heartRateValues = $this->getHeartRateValues();
        if($heartRateValues){
            return max($heartRateValues);
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

    public function clearTrackPoints(){
        $this->trackPoints = array();
    }



    public function getHeartRateValues(){
        $values = array();
        foreach($this->trackPoints as $trackPoint){
            if($trackPoint->getHeartRateBpm()){
                $values[] = $trackPoint->getHeartRateBpm();
            }
        }
        return $values;
    }

}
