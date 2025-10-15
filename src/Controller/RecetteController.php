<?php

namespace App\Controller;

use App\Entity\Recette;
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

        $creation_formulaire = $this->createForm(RecetteFormType::class, $recette,['submit_label' => 'Créer la recette']);
        $creation_formulaire->handleRequest($request);

        if ($creation_formulaire->isSubmitted() && $creation_formulaire->isValid()) {
            $recette->setCreatedAt(new \DateTimeImmutable());
            $recette->setUpdatedAt(new \DateTimeImmutable());

            $entity_manager->persist($recette);
            $entity_manager->flush();

            $this->addFlash('success', 'Votre recette a bien été créé avec succès !');
            return $this->redirectToRoute('all_recette');
        }
        return $this->render('recette/create.html.twig', [
            'formulaire' => $creation_formulaire->createView(),
        ]);
    }
}
