<?php

namespace App\Controller\Admin;

use App\Entity\Recette;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class RecetteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Recette::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),

            TextField::new('nom', 'Nom'),

            IntegerField::new('temps', 'Temps (min)'),

            TextEditorField::new('description', 'Description'),

            IntegerField::new('difficulte', 'Difficulté'),

            IntegerField::new('prix', 'Prix'),

            AssociationField::new('ingredients', 'Ingrédients')
                ->formatValue(function ($value, $entity) {
                    $ingredients = $entity->getIngredients();
                    $labels = [];
                    foreach ($ingredients as $ingredient) {
                        $labels[] = $ingredient->getNom(); // récupère le nom de chaque ingrédient
                    }
                    return implode(', ', $labels); // les sépare par des virgules
                })
                ->onlyOnIndex(),
        ];
    }

    // On souhaite ne plus avoir les 3 petits points en bout des lignes avec le dropdown menu pour éditer ou supprimer.
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined();
    }
}
