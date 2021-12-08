<?php

namespace App\Form;

use App\Dto\RegisterUserDto;
use App\Entity\User;
use App\Form\FieldType\PasswordRepeatedType;
use App\Form\FieldType\UserRoleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('role', UserRoleType::class)
            ->add('plainPassword', PasswordRepeatedType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDto::class,
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
    }
}
