<?php
/**
 * Created by PhpStorm.
 * User: lukasz
 * Date: 11.02.15
 * Time: 20:46
 */

namespace AppBundle\Controller;


use AppBundle\Base\WorkoutImport\Tcx\Parser;
use AppBundle\Base\WorkoutImport\Tracker\Endomondo\EndomondoAPI;
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
     * @Route("/file", name="workout_import_file")
     * @Method("GET")
     * @Template()
     */
    public function fileAction()
    {
        return array();
    }

    /**
     * handle ajax tcx uploads
     *
     * @Route("/file/ajax/upload", name="workout_import_file_ajax_upload")
     * @Method("POST")
     * @param Request $request
     * @return JsonResponse
     */
    public function fileAjaxUploadAction(Request $request){
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

        $fileContent = file_get_contents($file->getPathName());
        $tcxParser = new Parser(new \SimpleXMLElement($fileContent));
        $tcxParser->parse();
        $importService = $this->get('app.workout_import');
        foreach($tcxParser->getWorkouts() as $workout) {
            if (!$workout->isValid()) {
                $responseData[] = array(
                    'datetime' => $workout->getStartDateTime()->format('Y-m-d H:i:s'),
                    'message' => $this->get('translator')->trans('workout.import.file.invalidFile'),
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
                    'message' => $this->get('translator')->trans('workout.fileImport.duplicateWorkout'),
                    'debug' => null,
                    'success' => false
                );
                continue;
            }
            $importService->saveUserWorkout($workout, $this->getUser());
            $responseData[] = array(
                'datetime' => $workout->getStartDateTime()->format('Y-m-d H:i:s'),
                'message' => $this->get('translator')->trans('workout.fileImport.success'),
                'debug' => null,
                'success' => true
            );
        }
        $this->getDoctrine()->getManager()->flush();
        return new JsonResponse($responseData);
    }

}