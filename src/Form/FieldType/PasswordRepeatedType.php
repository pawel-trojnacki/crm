<?php

namespace App\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordRepeatedType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type' => PasswordType::class,
            // instead of being set onto the object directly,
            // this is read and encoded in the service
            // 'mapped' => false,
            'attr' => ['autocomplete' => 'new-password'],
            // 'label' => 'Password',
            'first_options'  => [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ],
            'second_options' => ['label' => 'Repeat Password'],
            'invalid_message' => 'Passwords do not match',
        ]);
    }

    public function getParent()
    {
        return RepeatedType::class;
    }
}
