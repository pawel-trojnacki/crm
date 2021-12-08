<?php

namespace App\Form;

use App\Dto\MeetingDto;
use App\Validator\MeetingEndTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('importance', TextType::class)
            ->add('beginAt', DateTimeType::class, [
                'widget' => 'single_text',
                'placeholder' => 'Select a value',
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('contact', TextType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MeetingDto::class,
        ]);
    }
}
