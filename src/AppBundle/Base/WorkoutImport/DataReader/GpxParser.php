<?php

namespace AppBundle\Base\WorkoutImport\DataReader;

use AppBundle\Base\WorkoutImport\Model\Workout;
use AppBundle\Base\WorkoutImport\Model\TrackPoint;

class GpxParser implements DataReaderInterface
{

    /**
     * @var \SimpleXMLElement
     */
    protected $xmlObj;

    public function __construct(\SimpleXMLElement $xmlObject)
    {
        $this->xmlObj = $xmlObject;
    }

    /**
     * @return \AppBundle\Base\WorkoutImport\Model\Workout[];
     */
    public function getWorkouts()
    {
        $workouts = array();
        if (isset($this->xmlObj->trk)) {
            $workouts[] = $this->parseTrack($this->xmlObj->trk);
        }
        return $workouts;
    }


    protected function parseTrack(\SimpleXMLElement $track)
    {
        $workout = new Workout();

        if (isset($track->type)) {
            $workout->setSport((string)$track->type);
        }

        $workout->setCalories(0);
        $workout->setDistanceMeters(0);
        $workout->setTotalTimeSeconds(0);

        if (isset($track->trkseg)) {
            foreach ($track->trkseg as $trackSegment) {
                $this->parseTrackSegment($trackSegment, $workout);
            }
        }
        return $workout;
    }

    protected function parseTrackSegment(\SimpleXMLElement $trackSegment, Workout $workout)
    {
        foreach ($trackSegment->trkpt as $trackPoint) {
            $parsedTrackPoint = $this->parseTrackPoint($trackPoint);
            if ($parsedTrackPoint) {
                $workout->addTrackPoint($parsedTrackPoint);
            }
        }

    }

    protected function parseTrackPoint(\SimpleXMLElement $trackPoint)
    {
        $lat = $trackPoint->attributes()->lat;
        $lng = $trackPoint->attributes()->lon;
        $time = $trackPoint->time;

        if (!$lat || !$lng || !$time) {
            return null;
        }

        $obj = new TrackPoint();
        $obj->setLat((string)$lat);
        $obj->setLng((string)$lng);
        $obj->setDateTime(new \DateTime((string)$time));
        if (isset($trackPoint->ele)) {
            $obj->setAltitudeMeters((int)$trackPoint->ele);
        }

        if(isset($trackPoint->extensions)){
            foreach ($trackPoint->extensions->children('http://www.garmin.com/xmlschemas/TrackPointExtension/v1') as $child) {
                if (isset($child->hr)) {
                    $obj->setHeartRateBpm((int)$child->hr);
                }
            }
        }

        return $obj;
    }
}