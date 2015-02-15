<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Sport;
use AppBundle\Form\SportType;



/**
 * Sport controller.
 *
 * @Route("/sport")
 * @Security("has_role('ROLE_USER')")
 */
class SportController extends Controller
{

    /**
     * Lists all Sport entities.
     *
     * @Route("/", name="sport")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Sport')->findBy(array('user' => $this->getUser()));

        $deleteForms = array();
        foreach ($entities as $entity) {
            $deleteForms[$entity->getId()] = $this->createDeleteForm($entity->getId())->createView();
        }

        return array(
            'entities' => $entities,
            'deleteForms' => $deleteForms
        );
    }
    /**
     * Creates a new Sport entity.
     *
     * @Route("/", name="sport_create")
     * @Method("POST")
     * @Template("AppBundle:Sport:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Sport();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('sport.flash.created'));
            return $this->redirect($this->generateUrl('sport'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Sport entity.
     *
     * @param Sport $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Sport $entity)
    {
        $form = $this->createForm(new SportType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('sport_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Sport entity.
     *
     * @Route("/new", name="sport_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Sport();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Sport entity.
     *
     * @Route("/{id}/edit", name="sport_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Sport')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Sport entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Sport entity.
    *
    * @param Sport $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Sport $entity)
    {
        $form = $this->createForm(new SportType($this->getUser()), $entity, array(
            'action' => $this->generateUrl('sport_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Sport entity.
     *
     * @Route("/{id}", name="sport_update")
     * @Method("PUT")
     * @Template("AppBundle:Sport:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Sport')->find($id);

        if (!$entity || !$entity->isOwnedBy($this->getUser())) {
            throw $this->createNotFoundException('Unable to find Sport entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('sport.flash.updated'));
            return $this->redirect($this->generateUrl('sport'));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }
    /**
     * Deletes a Sport entity.
     *
     * @Route("/{id}", name="sport_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Sport')->find($id);

            if (!$entity || !$entity->isOwnedBy($this->getUser())) {
                throw $this->createNotFoundException('Unable to find Sport entity.');
            }
            $em->remove($entity);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', $this->get('translator')->trans('sport.flash.deleted'));
        }

        return $this->redirect($this->generateUrl('sport'));
    }

    /**
     * Creates a form to delete a Sport entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sport_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'delete'))
            ->getForm()
        ;
    }
}
