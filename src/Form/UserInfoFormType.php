<?php

namespace App\Form;

use App\Dto\UpdateUserInfoDto;
use App\Entity\User;
use App\Form\FieldType\UserRoleType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserInfoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var UpdateUserInfoDto|null $user */
        $dto = $options['data'] ?? null;
        $help = null;

        if ($dto) {
            if (in_array(User::ROLE_ADMIN, $dto->roles)) {
                $data = 'Admin';
            } elseif (in_array(User::ROLE_MANAGER, $dto->roles)) {
                $data = 'Manager';
            } else {
                $data = 'User';
            }

            $help = 'Current user role: ' . $data;
        }

        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class);

        if ($options['with_roles']) {
            $builder->add('role', UserRoleType::class, [
                'help' => $help,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UpdateUserInfoDto::class,
            'with_roles' => false,
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
    }
}
