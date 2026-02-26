<?php

namespace App\Form;

use App\Entity\Etats;
use App\Entity\Lieux;
use App\Entity\Sorties;
use App\Entity\Villes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortiesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateDebut', null, [
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie',
            ])
            ->add('dateCloture', null, [
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription'
            ])
            ->add('nbInscriptionMax',null,[
            'label' => 'Nombre de palces',
        ])
            ->add('duree', null, [

        'label' => 'Durée (minutes)',
    ])
            ->add('description')


           // ->add('urlPhoto')

            ->add('villeOrganisatrice', null, [
                'mapped' => false,
                'disabled' => true,
                'label' => 'Ville organisatrice',
            ])

            ->add('noVilles', EntityType::class, [
                'class' => Villes::class,
                'mapped' => false,
                'label' => 'Ville',
                'choice_label' => 'nom_ville',
                'placeholder' => 'Choisir une ville',
            ])

            ->add('noLieux', EntityType::class, [
                'class' => Lieux::class,
                'label' => 'Lieux',
                'choice_label' => 'nomLieu',
                'placeholder' => 'Choisir un lieu',
                ])

            ->add('rue', TextType::class, [
                'mapped' => false,
                'disabled' => true,
                'required' => false,
                ])

            ->add('codePostal', TextType::class, [
                'mapped' => false,
                'disabled' => true,
                'required' => false,
                ])

            ->add('latitude', TextType::class, [
                'mapped' => false,
                'disabled' => true,
                'required' => false,
                ])

            ->add('longitude', TextType::class, [
                'mapped' => false,
                'disabled' => true,
                'required' => false,
                ])

            ->add('enregistrer', SubmitType::class, [
                'label' => 'Enregistrer',
            ])
            ->add('publier', SubmitType::class, [
                'label' => 'Publier',
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sorties::class,
        ]);
    }
}
