<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom'])
            ->add('email', EmailType::class, ['label' => 'Email'])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'data_class' => null,
                'mapped' => false,           // Important : on gère manuellement
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide',
                    ])
                ],
            ])
            //->add('image', TextType::class, ['label' => 'Image'])
            ->add('bornAt', BirthdayType::class, ['label' => 'Date de naissance'])
            ->add('city', TextType::class, ['label' => 'Ville', 'required' => false])
            ->add('country', CountryType::class, ['label' => 'Pays'])
            //->add('createdAt', null, ['widget' => 'single_text',])
            ->add('about', TextareaType::class, ['label' => 'A propos de moi', 'required' => false])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
            ->addEventListener(FormEvents::PRE_SUBMIT, $this->verifyImage(...) )
        ;
    }

    public function verifyImage(PreSubmitEvent $event): void
    {
        $data = $event->getData();
        if(empty($data['image'])){
            $data['image'] = null;
            $event->setData($data);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['Default'], // On ignore le groupe "registration"
        ]);
    }
}
