<?php

namespace App\Form;

use App\Dto\CompanyDto;
use App\Entity\Country;
use App\Entity\Industry;
use App\Repository\IndustryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyFormType extends AbstractType
{
    public function __construct(
        private IndustryRepository $industryRepository,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Comany name',
            ])
            ->add('industry', EntityType::class, [
                'class' => Industry::class,
                'choice_label' => 'name',
                'choices' => $this->industryRepository->findAllAlphabetically(),
                'placeholder' => 'Choose an industry',
                'required' => false,
            ])
            ->add('website', UrlType::class, [
                'required' => false,
            ])
            ->add('address', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => false,
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'placeholder' => 'Choose a country',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyDto::class,
            'attr' => [
                'autocomplete' => 'off',
            ],
        ]);
    }
}
