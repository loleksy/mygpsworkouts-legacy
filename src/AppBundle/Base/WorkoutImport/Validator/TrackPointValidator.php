<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 12.06.15
 * Time: 00:19
 */

namespace AppBundle\Base\WorkoutImport\Validator;


use AppBundle\Base\WorkoutImport\Model\TrackPoint;

class TrackPointValidator implements ValidatorInterface {

    /**
     * @var TrackPoint
     */
    protected $trackPoint;

    public function __construct(TrackPoint $trackPoint){
        $this->trackPoint = $trackPoint;
    }

    public function validate(){
        if(!$this->trackPoint->getLat() || !$this->trackPoint->getLng() || !$this->trackPoint->getDatetime()){
            return 'some trackPoint data is missing';
        }
        return null;
    }



}