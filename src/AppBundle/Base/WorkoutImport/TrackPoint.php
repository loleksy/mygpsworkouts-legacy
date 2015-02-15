<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 01.02.15
 * Time: 18:22
 */

namespace AppBundle\Base\WorkoutImport;


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
    protected $hearth_rate_bpm;

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
    public function getHearthRateBpm()
    {
        return $this->hearth_rate_bpm;
    }

    /**
     * @param mixed $hearth_rate_bpm
     */
    public function setHearthRateBpm($hearth_rate_bpm)
    {
        $this->hearth_rate_bpm = $hearth_rate_bpm;
    }

    public function isValid(){
        return $this->getLat() && $this->getLng() && $this->getDatetime();
    }


}