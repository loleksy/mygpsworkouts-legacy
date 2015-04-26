<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 11.02.15
 * Time: 20:46
 */

namespace AppBundle\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Workout import controller.
 *
 * @Route("/workout/import")
 * @Security("has_role('ROLE_USER')")
 */
class WorkoutImportController extends Controller {

    /**
     * Tcx import page
     *
     * @Route("/file", name="workout_import_file")
     * @Method("GET")
     * @Template()
     */
    public function fileAction()
    {
        return array();
    }



}