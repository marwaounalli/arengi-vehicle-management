<?php

namespace App\Form;

use App\Entity\Car;
use App\Enum\CarTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('brand', TextType::class)
            ->add('model', TextType::class)
            ->add('type', EnumType::class, [
                'class' => CarTypeEnum::class,
                'placeholder' => 'Choisir un type',
                'choice_label' => fn(CarTypeEnum $e) => match ($e) {
                    CarTypeEnum::Berline => 'Berline',
                    CarTypeEnum::Citadine => 'Citadine',
                    CarTypeEnum::Utilitaire => 'Utilitaire',
                },
            ])

            ->add('passengers', IntegerType::class)
            ->add('color', TextType::class)
        ;

        $updatePtraField = static function ($form, ?CarTypeEnum $type): void {
            if ($type !== CarTypeEnum::Utilitaire) {
                if ($form->has('ptra')) {
                    $form->remove('ptra');
                }
                return;
            }
            $form->add('ptra', IntegerType::class, [
                'required' => true,
                'label' => 'PTRA',
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($updatePtraField): void {
            $car = $event->getData();
            $updatePtraField($event->getForm(), $car?->getType());
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) use ($updatePtraField): void {
            $data = $event->getData();

            $type = null;
            if (is_array($data)) {
                $type = CarTypeEnum::tryFrom((string) ($data['type'] ?? ''));
            }

            $updatePtraField($event->getForm(), $type);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
