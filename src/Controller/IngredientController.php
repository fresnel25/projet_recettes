<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientFormType;
use App\Form\IngredientFormType_v3;
use App\Repository\IngredientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'all_ingredient')]
    public function index(IngredientRepository $ingredientRepo, LoggerInterface $logger): Response
    {
        $logger->info('AFFICHAGE DE TOUS LES INGREDIENTS');
        $ingredients = $ingredientRepo->findAll();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/greater_than_100', name: 'greater_than_100')]
    public function index_only_greater_than_100(IngredientRepository $ingredientRepo): Response
    {
        $ingredients = $ingredientRepo->findAll();
        $ingredients_100 = [];

        foreach ($ingredients as $ingredient) {
            if ($ingredient->getPrix() > 100) {
                $ingredients_100[] = $ingredient;
            }
        }

        return $this->render('ingredient/prix_1.html.twig', [
            'ingredients' => $ingredients_100,
        ]);
    }

    #[Route('/ingredient/greater_than_100_v2', name: 'greater_than_100_v2')]
    public function index_only_greater_than_100_v2(IngredientRepository $ingredientRepo): Response
    {
        $ingredients = $ingredientRepo->findAll();
        $collection_ingredient = new ArrayCollection($ingredients);

        $filteredCollection = $collection_ingredient->filter(static fn(Ingredient $ingredient): bool => $ingredient->getPrix() > 100);
        return $this->render('ingredient/prix_2.html.twig', ['ingredients' => $filteredCollection]);
    }


    #[Route('/ingredient/greater_than_100_v3', name: 'greater_than_100_v3')]
    public function index_only_greater_than_100_v3(IngredientRepository $ingredientRepo): Response
    {
        $ingredients = $ingredientRepo->findAll();

        $collection = new ArrayCollection($ingredients);

        $criteria = Criteria::create()->where(
            Criteria::expr()->gt("prix", 100)
        );

        // Appliquer le filtre
        $filtered = $collection->matching($criteria);

        return $this->render('ingredient/prix_3.html.twig', [
            'ingredients' => $filtered,
        ]);
    }

    // ########################################################  Create  ####################################################


    // ####################################### Première Méthode de création de formulaire #########################################
    // Création du formulaire
    #[Route('/ingredient/create', name: 'ingredient.create', methods: 'GET')]
    public function create(): Response
    {
        $create_form = $this->createFormBuilder()
            ->setAction($this->generateUrl('ingredient.store'))
            ->setMethod('POST')
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Ingredient'])
            ->getForm();

        return $this->render('ingredient/create.html.twig', ['formulaire' => $create_form->createView()]);
    }

    // Stockage des datas
    #[Route('/ingredient/store', name: 'ingredient.store', methods: 'POST')]
    public function store(Request $request, EntityManagerInterface $entity_manager): Response
    {
        // Création de l'objet
        $ingredient = new Ingredient();

        // Récupération des données des différents input du formulaire
        $data = $request->request->all();
        //dd($data);

        // Affectation des données
        $ingredient->setNom($data["form"]["nom"]);
        $ingredient->setPrix($data["form"]["prix"]);
        $ingredient->setCreatedAt(new \DateTimeImmutable());

        // Persistance et sauvegarde
        $entity_manager->persist($ingredient);
        $entity_manager->flush();

        return $this->redirectToRoute('all_ingredient');
    }

    // ####################################### Deuxième Méthode de création de formulaire #########################################
    #[Route('/igredient/create_store', name: 'ingredient.create_store', methods: ['GET', 'POST'])]
    public function create_store(Request $request, EntityManagerInterface $entity_manager): Response
    {
        $ingredient = new Ingredient();

        $create_formulaire = $this->createFormBuilder($ingredient)
            ->add('nom', TextType::class)
            ->add('prix', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Ingredient'])
            ->getForm();

        $create_formulaire->handleRequest($request);
        if ($create_formulaire->isSubmitted() && $create_formulaire->isValid()) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());
            $entity_manager->persist($ingredient);
            $entity_manager->flush();
            return $this->redirectToRoute('all_ingredient');
        }

        return $this->render('ingredient/create_store.html.twig', [
            'formulaire' => $create_formulaire->createView(),
        ]);
    }

    // ####################################### Troisième Méthode de création de formulaire #########################################
    #[Route('/ingredient/create_store_v2', name: 'ingredient.create_store_v2', methods: ['GET', 'POST'])]
    public function create_store_v2(Request $request, EntityManagerInterface $entity_manager): Response
    {
        $ingredient = new Ingredient();

        $create_formulaire = $this->createForm(IngredientFormType::class, $ingredient);

        $create_formulaire->handleRequest($request);

        if ($create_formulaire->isSubmitted() && $create_formulaire->isValid()) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());

            $entity_manager->persist($ingredient);
            $entity_manager->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès !');
            return $this->redirectToRoute('all_ingredient');
        }

        return $this->render('ingredient/create_store_v2.html.twig', [
            'formulaire' => $create_formulaire->createView(),
        ]);
    }

    #[Route('/profile/ingredient/create_store_v3', name: 'ingredient.create_store_v3', methods: ['GET', 'POST'])]
    public function create_store_v3(Request $request, EntityManagerInterface $entity_manager, LoggerInterface $logger): Response
    {
        $logger->info('CREATION D’UN NOUVEL INGREDIENT');
        $ingredient = new Ingredient();

        $create_formulaire = $this->createForm(IngredientFormType_v3::class, $ingredient, ['submit_label' => 'Créer l\'ingrédient']);

        $create_formulaire->handleRequest($request);

        if ($create_formulaire->isSubmitted() && $create_formulaire->isValid()) {
            $ingredient->setCreatedAt(new \DateTimeImmutable());

            $entity_manager->persist($ingredient);
            $entity_manager->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès !');
            return $this->redirectToRoute('all_ingredient');
        }

        return $this->render('ingredient/create_store_v3.html.twig', [
            'formulaire' => $create_formulaire->createView(),
        ]);
    }


    // ########################################################  Update  ##################################################

    #[Route('/administrateur/ingredient/edit/{id}', name: 'ingredient.edit', methods: ['GET', 'PUT'])]
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, IngredientRepository $ingredientRepo): Response
    {

        // 1. Récupérer l’ingrédient depuis la BDD
        $ingredient = $ingredientRepo->find($id);

        if (!$ingredient) {
            $this->addFlash('danger', "l'ingredient avec lid $id n'a pas été trouvé");
            return $this->redirectToRoute('all_ingredient');
        }

        // 2. Créer le formulaire, avec l’ingrédient déjà rempli
        $form = $this->createForm(IngredientFormType_v3::class, $ingredient, [
            'method' => 'PUT',
            'submit_label' => 'Modifier l\'ingrédient'
        ]);

        // 3. Traiter la requête
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Votre ingrédient a été modifié avec succès !');
            // Redirection vers la liste des ingrédients
            return $this->redirectToRoute('all_ingredient');
        }

        // 4. Afficher le même template que pour la création
        return $this->render('ingredient/create_store_v3.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }

    // ########################################################  Delete  ##################################################
    #[Route('/administrateur/ingredient/{id}', name: 'ingredient.delete', methods: 'DELETE')]
    public function delete(int $id, IngredientRepository $ingredientRepo, EntityManagerInterface $entityManager): Response
    {
        $ingredient = $ingredientRepo->find($id);

        if (!$ingredient) {
            $this->addFlash('danger', "l'ingredient avec l'id $id n'existe pas");
            return $this->redirectToRoute('all_ingredient');
        }

        $entityManager->remove($ingredient);
        $entityManager->flush();

        $this->addFlash('success', 'Votre ingrédient a été supprimé avec succès !');
        // Redirection vers la liste des ingrédients
        return $this->redirectToRoute('all_ingredient');
    }

    // ########################################################  fonction ayant une méthode personnalisée avec Query Builder ##################################################
    #[Route('/ingredient/tomate', name: 'tomate.ingredient')]
    public function index_ingredient_tomate(IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->find_ingredient_tomate();
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    #[Route('/ingredient/tomate_5')]
    public function index_ingredient_tomate_5(IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->find_ingredient_tomate_5();
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    #[Route('/ingredient/start_tomate')]
    public function index_ingredient_tom(IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->find_ingredient_tom5();
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    #[Route('/ingredient/price/{prix}')]
    public function index_ingredient_by_price($prix, IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->find_ingredient_by_price($prix);
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    #[Route('/ingredient/by_price/{prix}/by_name/{nom}')]
    public function index_ingredient_by_price_and_name($prix, $nom, IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->find_ingredient_by_price_and_name($prix, $nom);
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    // ########################################################  fonction ayant une méthode personnalisée avec Query Builder ##################################################
    #[Route('/ingredient/all_ingredient_sql', name: 'all.ingredient_sql')]
    public function all_Ingredient_sql(IngredientRepository $ingredientRepo): Response
    {
        $tomate = $ingredientRepo->findAll_sql();
        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $tomate,
        ]);
    }

    // ############################################################ Récupération de l'ingredient via le Slug ########################################### 
    #[Route('/ingredient/by_slug/{slug}', name: 'ingredient_show_by_slug')]
    public function showBySlug(String $slug, IngredientRepository $ingredientRepo): Response
    {
        $ingredient = $ingredientRepo->findOneBy(['slug' => $slug]);
        if (!$ingredient) {
            $this->addFlash('warning', 'Votre ingrédient n\'existe pas !');
            return $this->redirectToRoute('all_ingredient');
        }
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }

     // ############################################################ Récupération de l'ingredient via l'Id ########################################### 
    #[Route('/ingredient/by_id/{id}', name: 'ingredient_show_by_id')]
    public function showById(String $id, IngredientRepository $ingredientRepo): Response
    {
        $ingredient = $ingredientRepo->findOneBy(['id' => $id]);
        if (!$ingredient) {
            $this->addFlash('warning', 'Votre ingrédient n\'existe pas !');
            return $this->redirectToRoute('all_ingredient');
        }
        return $this->render('ingredient/show.html.twig', [
            'ingredient' => $ingredient,
        ]);
    }
}
