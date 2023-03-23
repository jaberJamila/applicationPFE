<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse de messagerie'
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise'
            ])
            ->add('quality', ChoiceType::class, [
                'choices' => [
                    'Chercheur' => 'Chercheur',
                    'Doctorant' => 'Doctorant',
                    'Master' => 'Master',
                    'PFE' => 'PFE',
                ],
                'label' => 'Qualité'
                //    'placeholder'=>

            ])
            ->add('iraId', TextType::class, [
                'label' => 'Identifiant de formation IRA'
            ])
            ->add('job', TextType::class, [
                'label' => 'Fonction / Poste'
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'label' => "Mot de passe"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
