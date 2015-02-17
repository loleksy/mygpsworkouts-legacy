<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 17.02.15
 * Time: 19:54
 */

namespace AppBundle\Twig;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('duration', array($this, 'durationFilter')),
            new \Twig_SimpleFilter('distance', array($this, 'distanceFilter')),
        );
    }

    public function durationFilter($value)
    {
        return gmdate("H:i:s", $value);
    }

    public function distanceFilter($value)
    {
        return (string)round($value/1000,2).' km';
    }

    public function getName()
    {
        return 'app_extension';
    }
}