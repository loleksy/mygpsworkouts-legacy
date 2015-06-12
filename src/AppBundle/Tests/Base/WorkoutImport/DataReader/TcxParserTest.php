<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 12.06.15
 * Time: 10:33
 */

namespace AppBundle\Tests\Base\WorkoutImport\DataReader;

use AppBundle\Base\WorkoutImport\DataReader\TcxParser;
use AppBundle\Tests\AbstractTestCase;
use AppBundle\Base\WorkoutImport\Model\TrackPoint;

class TcxParserTest  extends AbstractTestCase
{

    public function testValidRunning(){
        $parser = $this->prepareParser('tcx/valid_running.tcx');
        $workouts = $parser->getWorkouts();
        $this->assertCount(1, $workouts, 'File has only one workout');

        $workout = $workouts[0];
        $this->assertEquals('Running', $workout->getSport());
        $this->assertEquals(new \DateTime('2013-02-24 15:26:01'), $workout->getStartDateTime());
        $this->assertEquals(1063, $workout->getDistanceMeters());
        $this->assertEquals(89, $workout->getCalories());
        $this->assertEquals(430, $workout->getTotalTimeSeconds());

        $trackPoints = $workout->getTrackPoints();
        $this->checkValidTrackPoints($trackPoints, 43);

        $firstTrackPoint = $trackPoints[0];
        $this->assertEquals('50.192928', $firstTrackPoint->getLat());
        $this->assertEquals('19.096554', $firstTrackPoint->getLng());
        $this->assertEquals(new \DateTime('2013-02-24 15:26:01'), $firstTrackPoint->getDatetime());

        $lastTrackPoint  = $trackPoints[count($trackPoints)-1];
        $this->assertEquals('50.200005', $lastTrackPoint->getLat());
        $this->assertEquals('19.090282', $lastTrackPoint->getLng());
        $this->assertEquals(new \DateTime('2013-02-24 15:33:12'), $lastTrackPoint->getDatetime());
    }

    private function prepareParser($relativeUploadFixturePath){
        $filePath = $this->getAbsoluteUploadFixturePath($relativeUploadFixturePath);
        $fileContent = file_get_contents($filePath);
        $tcxParser = new TcxParser(new \SimpleXMLElement($fileContent));
        return $tcxParser;
    }

    /**
     * @param  TrackPoint[] $trackPoints
     * @param int $expectedCount
     */
    private function checkValidTrackPoints($trackPoints, $expectedCount){
        $this->assertCount($expectedCount, $trackPoints);
        $prevDt = null;
        foreach($trackPoints as $trackPoint){
            $this->assertGreaterThan(0, $trackPoint->getLat());
            $this->assertGreaterThan(0, $trackPoint->getLng());
            if($prevDt){
                $this->assertGreaterThan($prevDt->getTimestamp(), $trackPoint->getDatetime()->getTimestamp());
            }
            $prevDt = $trackPoint->getDatetime();
        }
    }


}