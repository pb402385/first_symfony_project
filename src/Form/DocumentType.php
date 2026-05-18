<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Document;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Nom'])
            ->add('abstract')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'label',           // Le champ affiché dans le select
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionnez une catégorie',
                'required' => false,                 // ou true selon ton besoin
                'attr' => [
                    'class' => 'form-select'
                ],
            ])
            //->add('file')
            ->add('file', FileType::class, [
                'label' => 'Fichier à uploader',
                'mapped' => false,           // Très important
                'required' => false,        // si on a déjà un fichier, pas besoin qu'il soit obligatoire
                'constraints' => [
                    new File([
                        'maxSize' => '15M',      // Tu peux augmenter si besoin
                        'mimeTypes' => [
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                            'application/msword',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ],
                        'mimeTypesMessage' => 'Format de fichier non autorisé.',
                    ])
                ],
                'attr' => [
                    'accept' => '.pdf,.jpg,.jpeg,.png,.webp,.doc,.docx,.xls,.xlsx'
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
