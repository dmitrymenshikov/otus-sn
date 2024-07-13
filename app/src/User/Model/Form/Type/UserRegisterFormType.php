<?php

declare(strict_types=1);

namespace App\User\Model\Form\Type;

use App\User\Model\Enum\GenderEnum;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;;
use App\User\Model\Form\DTO\UserRegisterTypeDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserRegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'required' => true
            ])
            ->add('password', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 4, 'max' => 10])
                ],
                'required' => true
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'required' => true
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'required' => true
            ])
            ->add('gender', EnumType::class, [
                'class' => GenderEnum::class,
                'required' => true
            ])
            ->add('birthday', DateType::class, [
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd.MM.yyyy',
                'html5' => false,
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'required' => true
            ])
            ->add('about', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2, 'max' => 255])
                ],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegisterTypeDTO::class,
        ]);
    }
}