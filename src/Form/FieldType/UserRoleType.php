<?php

namespace App\Form\FieldType;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    private const helpMessages = [
        User::ROLE_ADMIN =>
        <<<EOD
        Can create and edit all companies, contacts and notes,
        manage workspace, create and delete users.
        EOD,
        User::ROLE_MANAGER =>
        <<<EOD
        Can add new companies, contacts and notes, and edit
        and delete only those, that he create by himsef.
        EOD,
        User::ROLE_USER => 'Can only view companies, contacts, users etc.',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => false,
            'label_html' => true,
            'choices' => [
                'User' => User::ROLE_USER,
                'Manager' => User::ROLE_MANAGER,
                'Admin' => User::ROLE_ADMIN,
            ],
            'choice_label' => function ($choice, $key, $value) {
                $html = sprintf('<span>%s</span>', $key);

                $html .= sprintf(
                    '<span class="d-block fw-normal mb-2 text-muted">%s</span>',
                    self::helpMessages[$value]
                );

                return $html;
            },
            'label' => 'User role',
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
