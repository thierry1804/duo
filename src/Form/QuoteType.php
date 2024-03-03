<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false,
                ],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false,
                ],
            ])
            ->add('expedition', ChoiceType::class, [
                'label' => 'Mode d\'expédition',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    'Maritime' => 'maritime',
                    'Aérien' => [
                        'Aérien - Express' => 'aerien_express',
                        'Aérien - Normal' => 'aerien_normal',
                    ],
                ],
                'attr' => [
                    'class' => 'form-select',
                    'required' => true,
                    'size' => 4,
                ],
                'help' => "Choisissez le mode d'expédition que vous souhaitez. 
                    Sachez que le mode d'expédition aérien est plus rapide que le mode d'expédition maritime. 
                    Le mode aérien Express c'est entre 3 à 7 jours, le mode aérien Normal c'est entre 10 à 20 jours, 
                    tandis que le mode maritime c'est 45 à 65 jours environ.",
                'help_attr' => [
                    'class' => 'form-text fst-italic',
                ],
            ])
            ->add('transitaire', ChoiceType::class, [
                'label' => 'Transitaire',
                'required' => true,
                'expanded' => false,
                'multiple' => false,
                'choices' => [
                    'Votre transitaire' => 'other_transitaire',
                    'Transitaire partenaire de DUO' => 'duo_transitaire',
                ],
                'attr' => [
                    'class' => 'form-select',
                    'required' => true,
                    'size' => 2,
                ],
                'help' => "Choisissez le transitaire qui va traiter votre expédition. 
                    Si vous choisissez votre propre transitaire, n'oubliez pas de préciser son nom et son adresse dans le champs réservé à cela.",
                'help_attr' => [
                    'class' => 'form-text fst-italic',
                ],
            ])
            ->add('other_transitaire', TextareaType::class, [
                'label' => 'Votre transitaire',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'required' => false,
                ],
                'help' => "Remplissez uniquement si vous avez choisi un autre transitaire, autre que celui qui travaille avec DUO Import MDG.",
                'help_attr' => [
                    'class' => 'form-text fst-italic',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
