<?php

namespace App\Form;

use App\Entity\User;
use App\Form\FieldTypes\PasswordRepeatedType;
use App\Form\FieldTypes\UserRoleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['data'] ?? null;
        $help = null;

        if ($user) {
            if (in_array('ROLE_ADMIN', $user->getRoles())) {
                $data = 'Admin';
            } elseif (in_array('ROLE_MANAGER', $user->getRoles())) {
                $data = 'Manager';
            } else {
                $data = 'User';
            }

            $help = 'Current user role: ' . $data;
        }

        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('role', UserRoleType::class, [
                'help' => $help,
            ]);

        if ($options['with_password']) {
            $builder->add('plainPassword', PasswordRepeatedType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'with_password' => true,
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
    }
}
