<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('datetime', DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'constraints' => [
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date doit être aujourd\'hui ou ultérieure.'
                    ])
                ]
            ])
            ->add('capacity', IntegerType::class)
            ->add('is_public', CheckboxType::class, [
                'label' => 'Événement public',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['submit_label'],
                'attr' => ['class' => 'btn btn-success mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'submit_label' => 'Enregistrer', // Par défaut, le label du bouton est 'Enregistrer'
        ]);
    }
}
