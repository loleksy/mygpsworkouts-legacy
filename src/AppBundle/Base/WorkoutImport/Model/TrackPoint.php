<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 01.02.15
 * Time: 18:22
 */

namespace AppBundle\Base\WorkoutImport\Model;


class TrackPoint {

    /**
     * @var \DateTime
     */
    protected $datetime;

    /**
     * @var string
     */
    protected $lat;

    /**
     * @var string
     */
    protected $lng;

    /**
     * @var int
     */
    protected $altitude_meters;

    /**
     * @var
     */
    protected $heart_rate_bpm;

    /**
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }


    /**
     * @return string
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat($lat)
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     */
    public function setLng($lng)
    {
        $this->lng = $lng;
    }

    /**
     * @return int
     */
    public function getAltitudeMeters()
    {
        return $this->altitude_meters;
    }

    /**
     * @param int $altitude_meters
     */
    public function setAltitudeMeters($altitude_meters)
    {
        $this->altitude_meters = $altitude_meters;
    }

    /**
     * @return mixed
     */
    public function getHeartRateBpm()
    {
        return $this->heart_rate_bpm;
    }

    /**
     * @param mixed $heart_rate_bpm
     */
    public function setHeartRateBpm($heart_rate_bpm)
    {
        $this->heart_rate_bpm = $heart_rate_bpm;
    }


}