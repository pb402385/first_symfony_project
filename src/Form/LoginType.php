<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('_username', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'exemple@email.com'],
                'required' => true,
            ])
            ->add('_password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Se connecter',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    // ← Important : on désactive le mapping des données
    public function getBlockPrefix(): string
    {
        return '';
    }
}
