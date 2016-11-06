<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 15.02.15
 * Time: 12:12
 */

namespace AppBundle\Service;

use AppBundle\Base\WorkoutImport\DataReader\DataReaderInterface;
use AppBundle\Base\WorkoutImport\DataReader\GpxParser;
use AppBundle\Base\WorkoutImport\DataReader\TcxParser;
use AppBundle\Base\WorkoutImport\Validator\WorkoutValidator;
use \Doctrine\ORM\EntityManager;
use \AppBundle\Base\WorkoutImport\Model\Workout as ImportedWorkout;
use \AppBundle\Base\WorkoutImport\Model\TrackPoint as ImportedTrackPoint;
use \AppBundle\Entity\Workout as WorkoutEntity;
use \AppBundle\Entity\Sport as SportEntity;
use \AppBundle\Entity\Trackpoint as TrackPointEntity;
use Doctrine\ORM\EntityManagerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Translation\TranslatorInterface;

class WorkoutImportService {

    /**
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        UserInterface $user
    ) {
        $this->em = $entityManager;
        $this->translator = $translator;
        $this->user = $user;
    }

    public function importFile(UploadedFile $file = null)
    {
        $output = [];

        if(!$file){
            $output[] = $this->getOutputItem($this->translator->trans('workout.import.file.notUploaded'), false);
            return $output;
        }

        $reader = $this->getReader($file);

        if(!$reader){
            $output[] = $this->getOutputItem($this->translator->trans('workout.import.file.invalidFile'), false);
            return $output;
        }

        $workouts = $reader->getWorkouts();
        foreach($workouts as $workout){
            $validationOutput = $this->getValidationOutput($workout);

            if($validationOutput){
                $message = $this->translator->trans('workout.import.file.invalidFile');
                $output[] = $this->getOutputItem($message, false, $workout->getStartDateTime(), $validationOutput);
                continue;
            }

            if($this->isWorkoutDuplicated($workout)){
                $message = $this->translator->trans('workout.fileImport.duplicateWorkout');
                $output[] = $this->getOutputItem($message, false, $workout->getStartDateTime());
                continue;
            }

            $this->saveWorkoutEntity($workout);
            $message = $this->translator->trans('workout.fileImport.success');
            $output[] = $this->getOutputItem($message, true, $workout->getStartDateTime());
        }

        return $output;
    }

    /**
     * @param UploadedFile $file
     * @return DataReaderInterface|null
     */
    protected function getReader(UploadedFile $file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $content = file_get_contents($file->getPathName());

        switch($extension){
            case 'tcx':
                return new TcxParser(new \SimpleXMLElement($content));
            case 'gpx':
                return new GpxParser(new \SimpleXMLElement($content));
            default:
                return null;
        }
    }

    protected function getValidationOutput(ImportedWorkout $importedWorkout){
        $validator = new WorkoutValidator($importedWorkout);
        return $validator->validate();
    }

    protected function isWorkoutDuplicated(ImportedWorkout $importedWorkout){
        $duplicateEntity = $this->em->getRepository('AppBundle:Workout')->findOneBy(array(
            'user' => $this->user,
            'startDatetime' => $importedWorkout->getStartDateTime()
        ));
        return (bool)$duplicateEntity;
    }

    /**
     * @param ImportedWorkout $importedWorkout
     * @return WorkoutEntity
     */
    protected function saveWorkoutEntity(ImportedWorkout $importedWorkout){
        $workoutEntity = new WorkoutEntity();
        $workoutEntity->setUser($this->user);
        $workoutEntity->setSport($this->getSportEntity($importedWorkout));
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
     * @return SportEntity
     */
    protected function getSportEntity(ImportedWorkout $workout){
        $sport = $this->em->getRepository('AppBundle:Sport')->findOneBy(array(
            'user' => $this->user,
            'name' => $workout->getSport()
        ));
        if(!$sport){
            $sport = $this->saveSportEntity($workout);
        }
        return $sport;
    }

    /**
     * @param ImportedWorkout $workout
     * @return SportEntity
     */
    protected function saveSportEntity(ImportedWorkout $workout){
        $sport = new SportEntity();
        $sport->setUser($this->user);
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
        $trackPoint->setDatetime($importedTrackPoint->getDatetime());
        $trackPoint->setIndex($index);
        $trackPoint->setLat($importedTrackPoint->getLat());
        $trackPoint->setLng($importedTrackPoint->getLng());
        $trackPoint->setAltitudeMeters($importedTrackPoint->getAltitudeMeters());
        $trackPoint->setHeartRateBpm($importedTrackPoint->getHeartRateBpm());
        $trackPoint->setWorkout($workoutEntity);
        $this->em->persist($trackPoint);
        $workoutEntity->getTrackpoints()->add($trackPoint);
        return $trackPoint;
    }

    protected function getOutputItem($message, $success, $datetime = null, $debug = null)
    {
        return array(
            'message' => $message,
            'success' => $success,
            'datetime' => $datetime ? $datetime->format('Y-m-d H:i:s') : null,
            'debug' => $debug,
        );
    }
}