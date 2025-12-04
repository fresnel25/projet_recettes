<?php

namespace App\EventListener;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, method: 'prePersist', entity: Recette::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'preUpdate', entity: Recette::class)]
class RecetteListener
{
    public function prePersist(Recette $recette, PrePersistEventArgs $event): void
    {
        $now = new \DateTimeImmutable();
        $recette->setCreatedAt($now);
        $recette->setUpdatedAt($now);
    }

    public function preUpdate(Recette $recette, PreUpdateEventArgs $event): void
    {
        $recette->setUpdatedAt(new \DateTimeImmutable());
    }
}
