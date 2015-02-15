<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 11.02.15
 * Time: 20:46
 */

namespace AppBundle\Controller;


use AppBundle\Base\WorkoutImport\Tcx\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/tcx", name="workout_import_tcx")
     * @Method("GET")
     * @Template()
     */
    public function tcxAction()
    {
        return array();
    }

    /**
     * handle ajax tcx uploads
     *
     * @Route("/tcx/ajax/upload", name="workout_import_tcx_ajax_upload")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function tcxAjaxUploadAction(Request $request){
        $file = $request->files->get('file');
        $responseData = array();
        if(!$file){
            $responseData[] = array(
                'success' => false,
                'message' =>  $this->get('translator')->trans('workout.import.tcx.notUploaded'),
                'debug' => null,
                'datetime' => null
            );
            return new JsonResponse($responseData);
        }

        $tcxContent = file_get_contents($file->getPathName());
        $tcxParser = new Parser(new \SimpleXMLElement($tcxContent));
        $tcxParser->parse();
        $importService = $this->get('app.workout_import');
        foreach($tcxParser->getWorkouts() as $workout) {
            if (!$workout->isValid()) {
                $responseData[] = array(
                    'datetime' => $workout->getStartDateTime()->format('Y-m-d H:i:s'),
                    'message' => $this->get('translator')->trans('workout.import.tcx.invalidFile'),
                    'debug' => $workout->getErrorMessage(),
                    'success' => false,
                );
                continue;

            }
            $duplicateEntity = $this->getDoctrine()->getManager()->getRepository('AppBundle:Workout')->findOneBy(array(
                'user' => $this->getUser(),
                'startDatetime' => $workout->getStartDateTime()
            ));
            if($duplicateEntity) {
                $responseData[] = array(
                    'datetime' => $workout->getStartDateTime()->format('Y-m-d H:i:s'),
                    'message' => $this->get('translator')->trans('workout.tcxImport.duplicateWorkout'),
                    'debug' => null,
                    'success' => false
                );
                continue;
            }
            $importService->saveUserWorkout($workout, $this->getUser());
            $responseData[] = array(
                'datetime' => $workout->getStartDateTime()->format('Y-m-d H:i:s'),
                'message' => $this->get('translator')->trans('workout.tcxImport.success'),
                'debug' => null,
                'success' => true
            );
        }
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($responseData);
    }




}