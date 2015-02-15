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
use Symfony\Component\HttpFoundation\Request;

/**
 * Workout controller.
 *
 * @Route("/workout")
 * @Security("has_role('ROLE_USER')")
 */
class WorkoutController extends Controller {

    /**
     * Lists all workouts entities.
     * @Route("/", name="workout")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $pagination = $em->getRepository('AppBundle:Workout')->getPaginatedList(
            $this->getUser(),
            $this->get('knp_paginator'),
            $request->query->get('page', 1)
        );

//        $deleteForms = array();
//        foreach ($pagination as $entity) {
//            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
//        }

        return array(
            'pagination' => $pagination,
           // 'deleteForms' => $deleteForms
        );
    }

    /**
     * Deletes a Workout entity.
     *
     * @Route("/{id}", name="workout_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Workout')->find($id);

            if (!$entity || !$entity->isOwnedBy($this->getUser())) {
                throw $this->createNotFoundException('Unable to find Workout entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('workout'));
    }

    /**
     * Creates a form to delete a Workout entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('workout_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'delete'))
            ->getForm()
            ;
    }

}