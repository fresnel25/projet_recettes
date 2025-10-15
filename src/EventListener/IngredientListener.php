<?php

namespace App\EventListener;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Ingredient::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Ingredient::class)]
class IngredientListener
{
    // private $slugger;
    
    // public function __construct(SluggerInterface $slugger)
    // {
    //     $this->slugger = $slugger;
    // }

    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public function prePersist(Ingredient $ingredient, PrePersistEventArgs $event): void
    {
        // Met à jour la date
        $ingredient->setUpdatedAt(new \DateTimeImmutable());

        // Crée le slug lors de la création
        if ($ingredient->getNom()) {
            $slug = $this->slugger->slug($ingredient->getNom())->lower();
            $ingredient->setSlug($slug);
        }
    }

    public function preUpdate(Ingredient $ingredient, PreUpdateEventArgs $event): void
    {
        // Met à jour la date à chaque modification
        $ingredient->setUpdatedAt(new \DateTimeImmutable());

        // Met à jour le slug si le nom change
        if ($ingredient->getNom()) {
            $slug = $this->slugger->slug($ingredient->getNom())->lower();
            $ingredient->setSlug($slug);
        }
    }
}
