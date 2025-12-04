<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Entity\Tag;
use App\Form\RecetteFormType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecetteController extends AbstractController
{
    #[Route('/recette', name: 'all_recette')]
    public function index(RecetteRepository $recetteRepo): Response
    {
        $recettes = $recetteRepo->findAll();
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/ingredient', name: 'all.recette_sql')]
    public function all_recettes_ingredient(RecetteRepository $recetteRepo): Response
    {
        $recettes = $recetteRepo->findRecettesWithIngredients();
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/ingredient/DQL', name: 'all.recette_sql')]
    public function allrecettes_ingredient(RecetteRepository $recetteRepo): Response
    {
        $recettes = $recetteRepo->AllRecetteIngredient();
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/avec_5_ingredients', name: 'recette_sql')]
    public function recettes_5_ingredient(RecetteRepository $recetteRepo): Response
    {
        $recettes = $recetteRepo->Recette_5_Ingredient();
        return $this->render('recette/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    #[Route('/recette/create', name: 'recette.create_store', methods: ['GET', 'POST'])]
    public function create_store(Request $request, EntityManagerInterface $entity_manager): Response
    {
        $recette = new Recette();

        // On ajoute un tag vide par défaut pour afficher un champ dans le formulaire
        if ($recette->getTags()->isEmpty()) {
            $recette->getTags()->add(new Tag());
        }

        $form = $this->createForm(RecetteFormType::class, $recette, [
            'submit_label' => 'Créer la recette'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recette->setCreatedAt(new \DateTimeImmutable());
            $recette->setUpdatedAt(new \DateTimeImmutable());

            // On lie chaque tag à la recette
            foreach ($recette->getTags() as $tag) {
                $tag->setRecette($recette);
                $entity_manager->persist($tag);
            }

            $entity_manager->persist($recette);
            $entity_manager->flush();

            $this->addFlash('success', 'Votre recette a bien été créée avec succès !');
            return $this->redirectToRoute('all_recette');
        }

        return $this->render('recette/create.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
