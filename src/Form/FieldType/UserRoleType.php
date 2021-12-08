<?php

namespace App\Form\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRoleType extends AbstractType
{
    private const helpMessages = [
        'ROLE_ADMIN' =>
        <<<EOD
        Can create and edit all companies, contacts and notes,
        manage workspace, create and delete users.
        EOD,
        'ROLE_MANAGER' =>
        <<<EOD
        Can add new companies, contacts and notes, and edit
        and delete only those, that he create by himsef.
        EOD,
        'ROLE_USER' => 'Can only view companies, contacts, users etc.',
    ];

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'expanded' => true,
            'multiple' => false,
            'label_html' => true,
            'choices' => [
                'User' => 'ROLE_USER',
                'Manager' => 'ROLE_MANAGER',
                'Admin' => 'ROLE_ADMIN',
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
