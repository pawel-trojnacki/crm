<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Deal;
use App\Entity\User;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DealFormType extends AbstractType
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private UserRepository $userRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name', TextType::class)
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 4,
                ]
            ])
            ->add('stage', ChoiceType::class, [
                'choices' => array_combine(
                    array_map(fn ($stage) => ucwords($stage), Deal::STAGES),
                    Deal::STAGES
                ),
                'placeholder' => 'Choose a stage',
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'choices' => $this->companyRepository->findAllByWorkspaceAlphabetically(
                    $options['workspace']
                ),
                'placeholder' => 'Choose a company',
            ])
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findAllByWorkspaceAlphabetically(
                    $options['workspace']
                ),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Users assigned to the deal',
                'attr' => [
                    'class' => 'choices-flex',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Deal::class,
        ]);

        $resolver->setRequired(['workspace']);
    }
}
