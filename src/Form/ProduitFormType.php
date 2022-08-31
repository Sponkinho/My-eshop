<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('description')
            ->add('color', TextType::class, [
                'label' => 'Couleur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('size', ChoiceType::class, [
                'label' => 'Taille',
                'choices' => [
                    'S' => 's',
                    'M' => 'm',
                    'L' => 'l',
                    'XL' => 'xl',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('gender', ChoiceType::class, [
                'label' => 'Genre',
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('photo', FileType::class, [
                'label' => 'Photo du produit',
                
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix unitaire',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('stock', TextType::class, [
                'label' => 'Quantité en stock',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
                'validate' => false,
                'attr' => [
                    'class' => 'd-block mx-auto btn btn-primary col-3'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'allow_file_upload' => true, // Pour autoriser l'upload de fichiers
            'photo' => null,
        ]);
    }
}
