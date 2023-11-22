<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('mail',EmailType::class,[
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Email de l'utilisateur"
                ]
            ])

            ->add('password',TextType::class,[
                "label" => "Password",
                "attr" => [
                    "placeholder" => "Mot de passe de l'utilisateur"
                ]
            ]);

            // Custom form options to display conditionnally, if I'm updating a user I can't change their password
            /*if($options["custom_option"] !== "edit"){
                $builder
                    ->add('password',RepeatedType::class,[
                        "type" => PasswordType::class,
                        'invalid_message' => 'Les deux champs doivent être identiques',
                        'required' => true,
                        'first_options'  => ['label' => 'Le mot de passe',"attr" => ["placeholder" => "*****"]],
                        'second_options' => ['label' => 'Répétez le mot de passe',"attr" => ["placeholder" => "*****"]],
                    ]);
            }*/

            $builder
              ->add('role', EntityType::class, [
                'class' => Role::class,
                'choice_label' => 'name',
                'label' => 'Role',
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'custom_option' => "default"
        ]);
    }
}