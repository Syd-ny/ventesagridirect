<?php

namespace App\Form;


use App\Entity\Marque;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom',TextType::class,[
                "label" => "Nom de la marque",
                "attr" => [
                    "placeholder" => "Saisissez le nom de la marque"
                ]
            ])

            ->add('description',TextareaType::class,[
                "label" => "Description du produit",
                "attr" => [
                    "placeholder" => "Saisissez la desription de la marque"
                ]
            ])

            
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
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Marque::class,
            'custom_option' => "default"
        ]);

        
    }
}