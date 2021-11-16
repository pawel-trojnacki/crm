<?php

namespace App\Form;

use App\Entity\Company;
use App\Entity\Contact;
use App\Repository\CompanyRepository;
use App\Service\CompanyManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactFormType extends AbstractType
{
    public function __construct(
        private CompanyManager $companyManager,
    ) {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('position', TextType::class, [
                'required' => false,
            ])
            ->add('company', EntityType::class, [
                'class' => Company::class,
                'choice_label' => 'name',
                'choices' => $this->companyManager->findAllByWorkspaceAlphabetically(
                    $options['workspace']
                ),
                'placeholder' => 'Choose company',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);

        $resolver->setRequired(['workspace']);
    }
}
