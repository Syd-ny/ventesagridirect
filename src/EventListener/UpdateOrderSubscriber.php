<?php

namespace App\EventListener;

use App\Entity\Product;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;

class UpdateOrderSubscriber implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::SUBMIT => 'onFormSubmit',
        ];
    }

    public function onFormSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $product = $event->getData();

        // Récupérez la valeur de l'ordre soumis
        $order = $product->getOrdre();

        // Recherchez tous les autres produits ayant la même valeur d'ordre
        $productsWithSameOrder = $this->entityManager->getRepository(Product::class)->findBy(['ordre' => $order]);

        // Mettez à jour les valeurs d'ordre des autres produits pour les définir à null
        foreach ($productsWithSameOrder as $otherProduct) {
            if ($otherProduct !== $product) {
                $otherProduct->setOrdre(null);
            }
        }
    }
}

