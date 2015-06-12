<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Workout
 *
 * @ORM\Table(name="workout", indexes={@ORM\Index(name="fk_table1_users1_idx", columns={"user_id"}), @ORM\Index(name="fk_table1_sports1_idx", columns={"sport_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WorkoutRepository")
 */
class Workout implements \JsonSerializable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_datetime", type="datetime", nullable=false)
     */
    protected $startDatetime;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_time_seconds", type="integer", nullable=false)
     */
    protected $totalTimeSeconds;

    /**
     * @var integer
     *
     * @ORM\Column(name="distance_meters", type="integer", nullable=false)
     */
    protected $distanceMeters;

    /**
     * @var integer
     *
     * @ORM\Column(name="calories", type="integer", nullable=true)
     */
    protected $calories;

    /**
     * @var integer
     *
     * @ORM\Column(name="average_heart_rate_bpm", type="integer", nullable=true)
     */
    protected $averageHeartRateBpm;

    /**
     * @var integer
     *
     * @ORM\Column(name="maximum_heart_rate_bpm", type="integer", nullable=true)
     */
    protected $maximumHeartRateBpm;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \AppBundle\Entity\Sport
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Sport", inversedBy="workouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sport_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $sport;



    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="workouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    protected $user;
    
     /**
     * collection of Trackpoint
     * @var ArrayCollection|\AppBundle\Entity\Trackpoint[]
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Trackpoint", mappedBy="workout")
     * @ORM\JoinColumn(name="workout_id", referencedColumnName="id", nullable=false)
     */
    protected $trackpoints;
    
    
     /**
     * only construct object
     */
    public function __construct()
    {
        $this->trackpoints = new ArrayCollection();
    }



    /**
     * Set startDatetime
     *
     * @param \DateTime $startDatetime
     * @return Workout
     */
    public function setStartDatetime($startDatetime)
    {
        $this->startDatetime = $startDatetime;

        return $this;
    }

    /**
     * Get startDatetime
     *
     * @return \DateTime 
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Set totalTimeSeconds
     *
     * @param integer $totalTimeSeconds
     * @return Workout
     */
    public function setTotalTimeSeconds($totalTimeSeconds)
    {
        $this->totalTimeSeconds = $totalTimeSeconds;

        return $this;
    }

    /**
     * Get totalTimeSeconds
     *
     * @return integer 
     */
    public function getTotalTimeSeconds()
    {
        return $this->totalTimeSeconds;
    }

    /**
     * Set distanceMeters
     *
     * @param integer $distanceMeters
     * @return Workout
     */
    public function setDistanceMeters($distanceMeters)
    {
        $this->distanceMeters = $distanceMeters;

        return $this;
    }

    /**
     * Get distanceMeters
     *
     * @return integer 
     */
    public function getDistanceMeters()
    {
        return $this->distanceMeters;
    }

    /**
     * Set calories
     *
     * @param integer $calories
     * @return Workout
     */
    public function setCalories($calories)
    {
        $this->calories = $calories;

        return $this;
    }

    /**
     * Get calories
     *
     * @return integer 
     */
    public function getCalories()
    {
        return $this->calories;
    }

    /**
     * Set averageHeartRateBpm
     *
     * @param integer $averageHeartRateBpm
     * @return Workout
     */
    public function setAverageHeartRateBpm($averageHeartRateBpm)
    {
        $this->averageHeartRateBpm = $averageHeartRateBpm;

        return $this;
    }

    /**
     * Get averageHeartRateBpm
     *
     * @return integer 
     */
    public function getAverageHeartRateBpm()
    {
        return $this->averageHeartRateBpm;
    }

    /**
     * Set maximumHeartRateBpm
     *
     * @param integer $maximumHeartRateBpm
     * @return Workout
     */
    public function setMaximumHeartRateBpm($maximumHeartRateBpm)
    {
        $this->maximumHeartRateBpm = $maximumHeartRateBpm;

        return $this;
    }

    /**
     * Get maximumHeartRateBpm
     *
     * @return integer 
     */
    public function getMaximumHeartRateBpm()
    {
        return $this->maximumHeartRateBpm;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sport
     *
     * @param \AppBundle\Entity\Sport $sport
     * @return Workout
     */
    public function setSport(\AppBundle\Entity\Sport $sport = null)
    {
        $this->sport = $sport;

        return $this;
    }

    /**
     * Get sport
     *
     * @return \AppBundle\Entity\Sport 
     */
    public function getSport()
    {
        return $this->sport;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Workout
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * Add Trackpoint entity to collection (one to many).
     *
     * @param \AppBundle\Entity\Trackpoint $trackpoint
     * @return \AppBundle\Entity\Workout
     */
    public function addTrackpoint(\AppBundle\Entity\Trackpoint $trackpoint)
    {
        $trackpoint->setWorkout($this);
        $this->trackpoints[] = $trackpoint;

        return $this;
    }

    /**
     * remove Trackpoint entity from collection (one to many).
     *
     * @param \AppBundle\Entity\Trackpoint $trackpoint
     * @return \AppBundle\Entity\Workout
     */
    public function removeTrackpoint(\AppBundle\Entity\Trackpoint $trackpoint)
    {
        $this->trackpoints->removeElement($trackpoint);

        return $this;
    }

    /**
     * Get Trackpoint entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection|\AppBundle\Entity\Trackpoint[]
     */
    public function getTrackpoints()
    {
        return $this->trackpoints;
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'startTimestamp' => $this->getStartDatetime()->getTimestamp(),
            'distanceMeters' => $this->getDistanceMeters(),
            'totalTimeSeconds' => $this->getTotalTimeSeconds(),
            'sport' =>  array(
                'id' => $this->getSport()->getId()
            )
        );
    }
}
