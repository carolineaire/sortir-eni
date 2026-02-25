<?php

namespace App\Form;

use App\Entity\Participants;
use App\Entity\Sites;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;


class EditProfilFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Pseudo
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo',
                'constraints' => [
                    new NotBlank(message: 'Le pseudo est requis'),
                ],
            ])

            // Nom
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Le nom est requis'),
                ],
            ])

            // Prénom
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Le prénom est requis'),
                ],
            ])

            // Téléphone
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])

            // Email
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank(message: 'L\'email est requis'),
                ],
            ])

            // Ville de rattachement (Sites)
            ->add('noSites', EntityType::class, [
                'class' => Sites::class,
                'choice_label' => 'nomSite',
                'label' => 'Ville de rattachement',
                'placeholder' => 'Choisissez une ville',
                'constraints' => [
                    new NotBlank(message: 'Veuillez sélectionner une ville'),
                ],
            ])

            // Mot de passe actuel (pour vérification)
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'current-password'],
            ])

            // Nouveau mot de passe
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => [
                    'label' => 'Nouveau mot de passe',
                    'attr' => ['placeholder' => 'Laisser vide pour ne pas changer']
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => ['placeholder' => 'Confirmer votre nouveau mot de passe']
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;

        // Ajouter une validation dynamique pour le mot de passe actuel
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $currentPassword = $form->get('currentPassword')->getData();
            $plainPassword = $form->get('plainPassword')->getData();

            // Si on change le mot de passe, on doit vérifier le mot de passe actuel
            if (!empty($plainPassword) && empty($currentPassword)) {
                $form->get('currentPassword')->addError(
                    new \Symfony\Component\Form\FormError('Le mot de passe actuel est requis pour changer de mot de passe')
                );
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Participants::class,
        ]);
    }
}