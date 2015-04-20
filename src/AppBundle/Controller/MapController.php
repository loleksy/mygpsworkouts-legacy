<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 20.04.15
 * Time: 20:33
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Sport controller.
 *
 * @Route("/map")
 * @Security("has_role('ROLE_USER')")
 */
class MapController extends Controller
{

    /**
     * Workouts map view
     *
     * @Route("/", name="map")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sportEntities = $em->getRepository('AppBundle:Sport')->findBy(array('user' => $this->getUser()));
        return array(
            'sportEntities' => $sportEntities
        );
    }
}