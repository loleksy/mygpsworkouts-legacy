<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 15.02.15
 * Time: 12:12
 */

namespace AppBundle\Service;

use AppBundle\Base\WorkoutImport\Validator\WorkoutValidator;
use \Doctrine\ORM\EntityManager;
use \AppBundle\Entity\User;
use \AppBundle\Base\WorkoutImport\Model\Workout as ImportedWorkout;
use \AppBundle\Base\WorkoutImport\Model\TrackPoint as ImportedTrackPoint;
use \AppBundle\Entity\Workout as WorkoutEntity;
use \AppBundle\Entity\Sport as SportEntity;
use \AppBundle\Entity\Trackpoint as TrackPointEntity;

class WorkoutImportService {

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * @var string|null
     */
    protected $lastValidationErrorMessage;

    const IMPORT_RESULT_SUCCESS = 0;
    const IMPORT_RESULT_DUPLICATE = 1;
    const IMPORT_RESULT_INVALID = 2;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function importWorkout(ImportedWorkout $importedWorkout, User $user){
        if(!$this->isWorkoutValid($importedWorkout)){
            return static::IMPORT_RESULT_INVALID;
        }
        if($this->isWorkoutDuplicated($importedWorkout, $user)){
            return static::IMPORT_RESULT_DUPLICATE;
        }
        $this->saveWorkoutEntity($importedWorkout, $user);
        return static::IMPORT_RESULT_SUCCESS;
    }

    public function getLastValidationErrorMessage(){
        return $this->lastValidationErrorMessage;
    }

    protected function isWorkoutValid(ImportedWorkout $importedWorkout){
        $validator = new WorkoutValidator($importedWorkout);
        $this->lastValidationErrorMessage = $validator->validate();
        return (bool)!$this->lastValidationErrorMessage;
    }

    protected function isWorkoutDuplicated(ImportedWorkout $importedWorkout, User $user){
        $duplicateEntity = $this->em->getRepository('AppBundle:Workout')->findOneBy(array(
            'user' => $user,
            'startDatetime' => $importedWorkout->getStartDateTime()
        ));
        return (bool)$duplicateEntity;
    }



    /**
     * @param ImportedWorkout $importedWorkout
     * @param User $user
     * @return WorkoutEntity
     */
    protected function saveWorkoutEntity(ImportedWorkout $importedWorkout, User $user){
        $workoutEntity = new WorkoutEntity();
        $workoutEntity->setUser($user);
        $workoutEntity->setSport($this->getSportEntity($importedWorkout, $user));
        $workoutEntity->setStartDatetime($importedWorkout->getStartDateTime());
        $workoutEntity->setTotalTimeSeconds($importedWorkout->getTotalTimeSeconds());
        $workoutEntity->setDistanceMeters($importedWorkout->getDistanceMeters());
        $workoutEntity->setCalories($importedWorkout->getCalories());
        $workoutEntity->setAverageHeartRateBpm($importedWorkout->getAverageHeartRateBpm());
        $workoutEntity->setMaximumHeartRateBpm($importedWorkout->getMaximumHeartRateBpm());
        $this->em->persist($workoutEntity);
        $index=0;
        foreach($importedWorkout->getTrackPoints() as $importedTrackPoint){
            $this->saveTrackpointEntity($importedTrackPoint, $workoutEntity, $index);
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
            $sport = $this->saveSportEntity($workout,$user);
        }
        return $sport;
    }

    /**
     * @param ImportedWorkout $workout
     * @param User $user
     * @return SportEntity
     */
    protected function saveSportEntity(ImportedWorkout $workout, User $user){
        $sport = new SportEntity();
        $sport->setUser($user);
        $sport->setName($workout->getSport());
        $sport->setDisplayName($workout->getSport());
        $sport->setColor($this->getRandomSportColor());
        $this->em->persist($sport);
        return $sport;
    }

    protected function getRandomSportColor(){
        return '#'.dechex(rand(0x000000, 0xFFFFFF));
    }

    /**
     * @param ImportedTrackPoint $importedTrackPoint
     * @param WorkoutEntity $workoutEntity
     * @param $index
     * @return TrackPointEntity
     */
    protected function saveTrackPointEntity(ImportedTrackPoint $importedTrackPoint, WorkoutEntity $workoutEntity, $index){
        $trackPoint = new TrackpointEntity();
        $trackPoint->setWorkout($workoutEntity);
        $trackPoint->setDatetime($importedTrackPoint->getDatetime());
        $trackPoint->setIndex($index);
        $trackPoint->setLat($importedTrackPoint->getLat());
        $trackPoint->setLng($importedTrackPoint->getLng());
        $trackPoint->setAltitudeMeters($importedTrackPoint->getAltitudeMeters());
        $trackPoint->setHeartRateBpm($importedTrackPoint->getHeartRateBpm());
        $this->em->persist($trackPoint);
        return $trackPoint;
    }



}