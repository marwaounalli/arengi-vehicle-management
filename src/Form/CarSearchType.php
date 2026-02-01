<?php

namespace App\Form;

use App\Model\CarSearchCriteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CarSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class, [
                'required' => false,
                'label' => 'Marque',
            ])
            ->add('minPassengers', IntegerType::class, [
                'required' => false,
                'label' => 'Passagers min',
                'attr' => ['min' => 1],
            ])
            ->add('sort', ChoiceType::class, [
                'required' => false,
                'label' => 'Trier par',
                'choices' => [
                    'Derniers créés' => 'createdAt',
                    'Marque' => 'brand',
                    'Modèle' => 'model',
                    'Passagers' => 'passengers',
                ],
            ])
            ->add('dir', ChoiceType::class, [
                'required' => false,
                'label' => 'Ordre',
                'choices' => [
                    'Desc' => 'desc',
                    'Asc' => 'asc',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarSearchCriteria::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
