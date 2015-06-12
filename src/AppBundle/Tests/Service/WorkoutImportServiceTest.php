<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 12.06.15
 * Time: 11:52
 */

namespace AppBundle\Tests\Service;

use AppBundle\Base\WorkoutImport\Model\TrackPoint;
use AppBundle\Base\WorkoutImport\Model\Workout;
use AppBundle\Service\WorkoutImportService;
use AppBundle\Tests\AbstractTestCase;


class WorkoutImportServiceTest  extends AbstractTestCase{


    /**
     * @var \Mockery\MockInterface
     */
    protected $entityManagerMock;

    /**
     * @var \Mockery\MockInterface
     */
    protected $workoutRepositoryMock;

    /**
     * @var \Mockery\MockInterface
     */
    protected $sportRepositoryMock;

    /**
     * @var WorkoutImportService
     */
    protected $importService;

    /**
     * @var \Mockery\MockInterface
     */
    protected $userEntityMock;

    /**
     * @var \Mockery\MockInterface
     */
    protected $sportEntityMock;

    public function setUp(){
        parent::setUp();
        $this->entityManagerMock = \Mockery::mock('\Doctrine\ORM\EntityManager');
        $this->userEntityMock = \Mockery::Mock('\AppBundle\Entity\User');
        $this->workoutRepositoryMock = \Mockery::Mock('\AppBundle\Repository\WorkoutRepository');
        $this->sportEntityMock = \Mockery::Mock('\AppBundle\Entity\Sport');
        $this->sportRepositoryMock = \Mockery::Mock('\AppBundle\Repository\SportRepository');
        $this->importService = new WorkoutImportService($this->entityManagerMock);
    }

    public function testSuccessImport(){
        $workout = $this->getValidWorkout();

        //mock for workout duplicate check
        $duplicateCheckParams = array('user' => $this->userEntityMock, 'startDatetime' => $workout->getStartDateTime());
        $this->workoutRepositoryMock->shouldReceive('findOneBy')->with($duplicateCheckParams)->once()->andReturn(null);
        $this->entityManagerMock->shouldReceive('getRepository')->with('AppBundle:Workout')->once()->andReturn($this->workoutRepositoryMock);

        //mock for sport entity get
        $sportGetParams = array('user' => $this->userEntityMock, 'name' => $workout->getSport());
        $this->sportRepositoryMock->shouldReceive('findOneBy')->with($sportGetParams)->once()->andReturn($this->sportEntityMock);
        $this->entityManagerMock->shouldReceive('getRepository')->with('AppBundle:Sport')->once()->andReturn($this->sportRepositoryMock);

        //mock for entity persist
        $this->entityManagerMock->shouldReceive('persist')->times(11)->andReturn(null);
        $result = $this->importService->importWorkout($workout, $this->userEntityMock);
        $this->AssertEquals(WorkoutImportService::IMPORT_RESULT_SUCCESS, $result);

        //check importedWorkout
        $workoutEntity = $this->importService->getLastImportedWorkout();
        $this->assertEquals($this->userEntityMock, $workoutEntity->getUser());
        $this->AssertEquals($this->sportEntityMock, $workoutEntity->getSport());
        $this->assertEquals($workout->getCalories(), $workoutEntity->getCalories());
        $this->assertEquals($workout->getStartDateTime(), $workoutEntity->getStartDatetime());
        $this->assertEquals($workout->getDistanceMeters(), $workoutEntity->getDistanceMeters());
        $this->assertEquals($workout->getTotalTimeSeconds(), $workoutEntity->getTotalTimeSeconds());

        $trackPointEntities = $workoutEntity->getTrackpoints();
        $trackPoints = $workout->getTrackPoints();
        $this->assertCount(10, $trackPointEntities);
        for($i=0; $i<10; $i++){
            $this->assertEquals($trackPoints[$i]->getDatetime(), $trackPointEntities[$i]->getDatetime());
            $this->assertEquals($trackPoints[$i]->getLat(), $trackPointEntities[$i]->getLat());
            $this->assertEquals($trackPoints[$i]->getLng(), $trackPointEntities[$i]->getLng());
        }
    }

    public function testDuplicatedImport(){
        $workout = $this->getValidWorkout();

        //mock for workout duplicate check
        $duplicateCheckParams = array('user' => $this->userEntityMock, 'startDatetime' => $workout->getStartDateTime());
        $workoutMock = \Mockery::Mock('\AppBundle\Entity\Workout');
        $this->workoutRepositoryMock->shouldReceive('findOneBy')->with($duplicateCheckParams)->once()->andReturn($workoutMock);
        $this->entityManagerMock->shouldReceive('getRepository')->with('AppBundle:Workout')->once()->andReturn($this->workoutRepositoryMock);

        $result = $this->importService->importWorkout($workout, $this->userEntityMock);
        $this->AssertEquals(WorkoutImportService::IMPORT_RESULT_DUPLICATE, $result);
    }

    public function testInvalidWorkoutNoTrackPoints(){
        $workout = $this->getValidWorkout();
        $workout->clearTrackPoints();
        $result = $this->importService->importWorkout($workout, $this->userEntityMock);
        $this->AssertEquals(WorkoutImportService::IMPORT_RESULT_INVALID, $result);
    }

    public function testInvalidWorkoutNoStartDateTime(){
        $workout = $this->getValidWorkout();
        $workout->setStartDateTime(null);
        $result = $this->importService->importWorkout($workout, $this->userEntityMock);
        $this->AssertEquals(WorkoutImportService::IMPORT_RESULT_INVALID, $result);
    }

    private function getValidWorkout(){
        $workout = new Workout();
        $workout->setSport('Running');
        $workout->setCalories(50);
        $startDt = new \DateTime('2015-01-01 00:00:00');
        $workout->setStartDateTime($startDt);
        $workout->setDistanceMeters(500);
        $workout->setTotalTimeSeconds(120);
        $startLat = '50';
        $startLng = '20';
        for($i=0; $i<10; $i++){
            $trackPoint = new TrackPoint();
            $trackPoint->setLat($startLat.'.0'.$i);
            $trackPoint->setLng($startLng.'.0'.$i);
            $dt = clone $startDt;
            $dt->add(new \DateInterval('PT'.(12*$i).'S'));
            $trackPoint->setDatetime($dt);
            $workout->addTrackPoint($trackPoint);
        }
        return $workout;
    }


}