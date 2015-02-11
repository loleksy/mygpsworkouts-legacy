<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SportType extends AbstractType
{

    protected $user;

    public function __construct($user){
        $this->user = $user;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sport.form.name.label'
            ))
            ->add('displayName', 'text', array(
                'label' => 'sport.form.displayName.label'
            ))
            ->add('color', 'text', array(
                'label' => 'sport.form.color.label',
                'attr'  => array(
                    'data-toggle' => 'colorpicker'
                )
            ))
        ;

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                //$form = $event->getForm();
                $data = $event->getData();
                $data->setUser($this->user);
            }
        );
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Sport'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_sport';
    }
}
