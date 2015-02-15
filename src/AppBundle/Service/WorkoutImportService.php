<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 15.02.15
 * Time: 12:12
 */

namespace AppBundle\Service;

use \Doctrine\ORM\EntityManager;
use \AppBundle\Entity\User;
use \AppBundle\Base\WorkoutImport\Workout as ImportedWorkout;
use \AppBundle\Base\WorkoutImport\TrackPoint as ImportedTrackpoint;
use \AppBundle\Entity\Workout as WorkoutEntity;
use \AppBundle\Entity\Sport as SportEntity;
use \AppBundle\Entity\Trackpoint as TrackpointEntity;

class WorkoutImportService {

    /**
     *
     * @var EntityManager
     */
    protected $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param ImportedWorkout $importedWorkout
     * @param User $user
     * @return WorkoutEntity
     */
    public function saveUserWorkout(ImportedWorkout $importedWorkout, User $user){
        $workoutEntity = new WorkoutEntity();
        $workoutEntity->setUser($user);
        $workoutEntity->setSport($this->getSportEntity($importedWorkout, $user));
        $workoutEntity->setStartDatetime($importedWorkout->getStartDateTime());
        $workoutEntity->setTotalTimeSeconds($importedWorkout->getTotalTimeSeconds());
        $workoutEntity->setDistanceMeters($importedWorkout->getDistanceMeters());
        $workoutEntity->setCalories($importedWorkout->getCalories());
        $workoutEntity->setAverageHearthRateBpm($importedWorkout->getAverageHeartRateBpm());
        $workoutEntity->setMaximumHearthRateBpm($importedWorkout->getMaximumHeartRateBpm());
        $this->em->persist($workoutEntity);
        $index=0;
        foreach($importedWorkout->getTrackPoints() as $importedTrackPoint){
            $this->generateTrackpointEntity($importedTrackPoint, $workoutEntity, $index);
            $index+=1;
        }
        return $workoutEntity;
    }

    /**
     * @param ImportedWorkout $workout
     * @param User $user
     * @return SportEntity
     */
    protected function getSportEntity(ImportedWorkout $workout, User $user){
        $sport = $this->em->getRepository('AppBundle:Sport')->findOneBy(array(
            'user' => $user,
            'name' => $workout->getSport()
        ));
        if(!$sport){
            $sport = $this->generateSportEntity($workout,$user);
        }
        return $sport;
    }

    /**
     * @param ImportedWorkout $workout
     * @param User $user
     * @return SportEntity
     */
    protected function generateSportEntity(ImportedWorkout $workout, User $user){
        $sport = new SportEntity();
        $sport->setUser($user);
        $sport->setName($workout->getSport());
        $sport->setDisplayName($workout->getSport());
        $sport->setColor('#'.dechex(rand(0x000000, 0xFFFFFF)));
        $this->em->persist($sport);
        return $sport;
    }

    /**
     * @param ImportedTrackpoint $importedTrackpoint
     * @param WorkoutEntity $workoutEntity
     * @param $index
     * @return TrackpointEntity
     */
    protected function generateTrackpointEntity(ImportedTrackpoint $importedTrackpoint, WorkoutEntity $workoutEntity, $index){
        $trackpoint = new TrackpointEntity();
        $trackpoint->setWorkout($workoutEntity);
        $trackpoint->setDatetime($importedTrackpoint->getDatetime());
        $trackpoint->setIndex($index);
        $trackpoint->setLat($importedTrackpoint->getLat());
        $trackpoint->setLng($importedTrackpoint->getLng());
        $trackpoint->setAltitudeMeters($importedTrackpoint->getAltitudeMeters());
        $trackpoint->setHearthRateBpm($importedTrackpoint->getHearthRateBpm());
        $this->em->persist($trackpoint);
        return $trackpoint;
    }



}