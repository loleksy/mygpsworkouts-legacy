<?php

namespace AppBundle\Entity;

use AppBundle\Base\UserPermissionCheckInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sport
 *
 * @ORM\Table(name="sport", indexes={@ORM\Index(name="fk_sports_users_idx", columns={"user_id"})})
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"user", "name"},
 *     errorPath="name",
 *     message="sport.name.taken"
 * )
 */
class Sport implements UserPermissionCheckInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=255, nullable=true)
     * @Assert\NotNull()
     */
    protected $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=7, nullable=true)
     * @Assert\NotNull()
     */
    protected $color;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="sports")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    protected $user;

    /**
     * collection of Workout
     * @var ArrayCollection|\AppBundle\Entity\Workout[]
     *
     * @ORM\OneToMany(targetEntity="Workout", mappedBy="sport")
     * @ORM\JoinColumn(name="sport_id", referencedColumnName="id", nullable=false)
     */
    protected $workouts;



    /**
     * Set name
     *
     * @param string $name
     * @return Sport
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set displayName
     *
     * @param string $displayName
     * @return Sport
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * Get displayName
     *
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return Sport
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
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
     * Add Workout entity to collection (one to many).
     *
     * @param \AppBundle\Entity\Workout $workout
     * @return \AppBundle\Entity\Sport
     */
    public function addWorkout(\AppBundle\Entity\Workout $workout)
    {
        $workout->setSport($this);
        $this->workouts[] = $workout;

        return $this;
    }

    /**
     * remove Workout entity from collection (one to many).
     *
     * @param \AppBundle\Entity\Workout $workout
     * @return \AppBundle\Entity\Sport
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

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     * @return Sport
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

    public function isOwnedBy(User $user){
        return $this->getUser()->getId() === $user->getId();
    }
}
