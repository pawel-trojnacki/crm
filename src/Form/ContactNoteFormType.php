<?php

namespace App\Form;

use App\Entity\ContactNote;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactNoteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => $options['label_text'],
                'attr' => [
                    'rows' => 5,
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save note',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactNote::class,
            'label_text' => 'Create note'
        ]);
    }
}
