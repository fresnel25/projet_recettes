<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $ingredients = [];
        for ($i = 0; $i < 100; $i++) {
            $ingredient = new Ingredient();
            $ingredient->setNom($faker->lexify('ingr_????'));
            $ingredient->setSlug("test slug");
            $ingredient->setPrix($faker->randomFloat(2, 0, 200));
            $ingredient->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($ingredient);
            $ingredients[] = $ingredient;
        }

        for ($i = 0; $i < 50; $i++) {
            $recette = new Recette();
            $recette->setNom($faker->words(3, true));
            $recette->setDifficulte($faker->numberBetween(1, 5));
            $recette->setDescription($faker->sentence(10));
            $recette->setTemps($faker->numberBetween(10, 180));
            $recette->setPrix($faker->randomFloat(2, 5, 500));
           // $recette->setCreatedAt(new \DateTimeImmutable());
            $recette->setUpdatedAt(new \DateTimeImmutable());

            $choixIngredients = $faker->randomElements($ingredients, $faker->numberBetween(2, 10));
            foreach ($choixIngredients as $ing) {
                $recette->addIngredient($ing);
            }

            $manager->persist($recette);
        }

        // Admin
        $admin = new User();
        $admin->setNom($faker->firstName());
        $admin->setPrenom($faker->lastName());
        $admin->setVille($faker->city());
        $admin->setCp($faker->postcode());
        $admin->setPassword($this->hasher->hashPassword($admin, '1234'));
        $admin->setRoles(["ROLE_ADMIN"]);
        $admin->setEmail("admin@a.com");
        $manager->persist($admin);

        // Users
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setNom($faker->firstName());
            $user->setPrenom($faker->lastName());
            $user->setVille($faker->city());
            $user->setCp($faker->postcode());
            $user->setPassword($this->hasher->hashPassword($user, '1234'));
            $user->setRoles(["ROLE_USER"]);
            $user->setEmail($faker->unique()->email());
            $manager->persist($user);
        }

        $manager->flush();
    }
}
