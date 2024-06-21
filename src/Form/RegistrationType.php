<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
            ])
//            ->add('roles')
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'invalid_message' => 'Les mots de passe ne correspondent pas.',
                'required' => true,
                'constraints' => [new NotBlank()],
            ])
            ->add('LastName', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('FirstName', TextType::class, [
                'label' => 'Prénom'
            ])
//            ->add('City')
//            ->add('Street')
//            ->add('StreetNumber')
//            ->add('RegistrationDate', null, [
//                'widget' => 'single_text',
//            ])
//            ->add('ApiActivated')
            ->add('CguAccepted', CheckboxType::class, [
                'label' => 'J\'accepte les CGU de GreenGoodies',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
