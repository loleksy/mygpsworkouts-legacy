<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 07.02.15
 * Time: 21:23
 */


namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    

	/**
     * collection of Sport
     * @var ArrayCollection|\AppBundle\Entity\Sport[]
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Sport", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $sports;

    /**
     * collection of Workout
     * @var ArrayCollection|\AppBundle\Entity\Workout[]
     * 
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Workout", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $workouts;

    public function __construct() {
        parent::__construct();
        $this->workouts = new ArrayCollection();
        $this->sports = new ArrayCollection();
    }
    
    /**
     * Add Sport entity to collection (one to many).
     *
     * @param \AppBundle\Entity\Sport $sport
     * @return \AppBundle\Entity\User
     */
    public function addSport(\AppBundle\Entity\Sport $sport)
    {
        $sport->setUser($this);
        $this->sports[] = $sport;

        return $this;
    }

    /**
     * remove Sport entity from collection (one to many).
     *
     * @param \AppBundle\Entity\Sport $sport
     * @return \AppBundle\Entity\User
     */
    public function removeSport(\AppBundle\Entity\Sport $sport)
    {
        $this->sports->removeElement($sport);

        return $this;
    }

    /**
     * Get Sport entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection|\AppBundle\Entity\Sport[]
     */
    public function getSports()
    {
        return $this->sports;
    }

    /**
     * Add Workout entity to collection (one to many).
     *
     * @param \AppBundle\Entity\Workout $workout
     * @return \AppBundle\Entity\User
     */
    public function addWorkout(\AppBundle\Entity\Workout $workout)
    {
        $workout->setUser($this);
        $this->workouts[] = $workout;

        return $this;
    }

    /**
     * remove Workout entity from collection (one to many).
     *
     * @param \AppBundle\Entity\Workout $workout
     * @return \AppBundle\Entity\User
     */
    public function removeWorkout(\AppBundle\Entity\Workout $workout)
    {
        $this->workouts->removeElement($workout);

        return $this;
    }

    /**
     * Get Workout entity collection (one to many).
     *
     * @return \Doctrine\Common\Collections\Collection|\AppBundle\Entity\Workout[]
     */
    public function getWorkouts()
    {
        return $this->workouts;
    }


}
