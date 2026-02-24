<?php

namespace App\Form;

use App\Entity\Sites;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champs User
            ->add('pseudo', TextType::class)
            ->add('nom', TextType::class, [
                'mapped' => false,
            ])
            ->add('prenom', TextType::class, [
                'mapped' => false,
            ])
            ->add('telephone', TelType::class, [
                'mapped' => false,
                'required' => false,
            ])

            ->add('email', EmailType::class)

            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(message: 'Please enter a password'),
                    new Length(min: 6, max: 4096),
                ],
            ])

            // Champs Participants (mapped = false)

            ->add('administrateur', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
            ])
            ->add('actif', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    'Actif' => true,
                    'Inactif' => false,
                ],
            ])
            ->add('site', EntityType::class,[
                'class' => Sites::class,
                'choice_label' => 'nomSite',
                'mapped' => false,
                'placeholder' => 'Choisissez un site', ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
