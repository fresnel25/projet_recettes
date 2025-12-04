<?php

namespace App\Controller\Admin;

use App\Entity\Ingredient;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class IngredientCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Ingredient::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom', 'Nom'),
            NumberField::new('prix', 'Prix'),
            TextField::new('slug')->hideOnIndex(),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Mis à jour le')->hideOnForm(),

            AssociationField::new('recettes', 'Recettes associées')
                ->formatValue(function ($value) {
                    if (!is_iterable($value)) {
                        return '';
                    }

                    $labels = [];
                    foreach ($value as $recette) {
                        $labels[] = $recette->getNom();
                    }

                    return implode(', ', $labels);
                })
                ->onlyOnIndex(),

            AssociationField::new('recettes', 'Recettes associées')
                ->onlyOnForms(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined();
    }
}
