<?php

namespace App\Form;

use App\Entity\Show;
use App\Entity\Artiste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ShowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('description',TextareaType::class,['required'=>'false'])
            ->add('illustration', FileType::class, [
                'label'=>'photo',
                'mapped'=>false,
                'required'=>false,
                'constraints'=>[
                    new File([
                        'maxSize'=>'2024k',
                        'mimeTypes'=>[
                            'image/jpeg',
                            'image/png',
                            'image/jpg'
                        ],
                        'mimeTypesMessage'=>'Veuillez insérer un image en .jpg ou .png'
                    ])
                ]
            ])
            ->add('artistes',EntityType::class,['class'=>Artiste::class,'expanded'=>'true','multiple'=>'true','choice_label' => 'nom'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Show::class,
        ]);
    }
}
