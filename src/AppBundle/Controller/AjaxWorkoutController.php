<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 21.04.15
 * Time: 22:51
 */

namespace AppBundle\Controller;


use AppBundle\Base\WorkoutImport\DataReader\TcxParser;
use AppBundle\Base\WorkoutImport\DataReader\GpxParser;
use AppBundle\Service\WorkoutImportService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;



/**
 * Workout import controller.
 *
 * @Route("/ajax/workout")
 * @Security("has_role('ROLE_USER')")
 */
class AjaxWorkoutController extends Controller
{

    /**
     * handle ajax tcx uploads
     *
     * @Route("", name="ajax_workout_upload")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function fileAjaxUploadAction(Request $request){
        $file = $request->files->get('file');
        $importService = $this->get('app.workout_import');
        $output = $importService->importFile($file);

        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($output);
    }

    /**
     * Returns specified workout
     *
     * @Route("/{id}", name="ajax_workout_view")
     * @Method("GET")
     */
    public function viewWorkoutAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Workout')->find($id);

        if (!$entity || !$this->get('security.authorization_checker')->isGranted('view', $entity)){
            throw $this->createNotFoundException('Unable to find Workout entity.');
        }
        return new JsonResponse($entity);
    }

    /**
     * Returns specified workout trackpoints
     *
     * @Route("/{id}/trackpoint", name="ajax_workout_trackpoints_view")
     * @Method("GET")
     */
    public function viewTrackpointsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Workout')->find($id);

        if (!$entity || !$this->get('security.authorization_checker')->isGranted('view', $entity)){
            throw $this->createNotFoundException('Unable to find Workout entity.');
        }
        return new JsonResponse($entity->getTrackpoints()->toArray());
    }

    /**
     * Search workouts
     *
     * @Route("", name="ajax_workout_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $tz = new \DateTimeZone('UTC');
        $startTs = $request->get('start_ts', null);
        $startDt = $startTs? \DateTime::createFromFormat('U', $startTs, $tz):null;
        $endTs = $request->get('end_ts', null);
        $endDt = $endTs? \DateTime::createFromFormat('U', $endTs, $tz):null;
        $sportIdsString = $request->get('sport_ids', '');
        $sportIds = $sportIdsString?array_map('intval', explode(',', $sportIdsString)):array();
        $results = $em->getRepository('AppBundle:Workout')->search($user, $startDt, $endDt, $sportIds);
        return new JsonResponse($results);
    }
}