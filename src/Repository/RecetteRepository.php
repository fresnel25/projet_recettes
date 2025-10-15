<?php

namespace App\Repository;

use App\Entity\Recette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recette>
 *
 * @method Recette|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recette|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recette[]    findAll()
 * @method Recette[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recette::class);
    }

    //    /**
    //     * @return Recette[] Returns an array of Recette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Recette
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    // Utilisation du QueryBuilder pour éffectuer des requêtes
    public function findRecettesWithIngredients(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT r.id AS recette_id, r.nom AS recette_nom, i.id AS ingredient_id, i.nom AS ingredient_nom
    FROM recette r
    JOIN recette_ingredient ri ON r.id = ri.recette_id
    JOIN ingredient i ON ri.ingredient_id = i.id
';


        $resultSet = $conn->executeQuery($sql);
        return $resultSet->fetchAllAssociative();
    }

    // CreateBuilder DQL
    public function AllRecetteIngredient(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT r, i FROM App\Entity\Recette r JOIN r.ingredients i'
        );
        $result = $query->getResult();

        return $result;
    }

    // CreateBuilder DQL
    public function Recette_5_Ingredient(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT r 
         FROM App\Entity\Recette r 
         JOIN r.ingredients i 
         GROUP BY r.id 
         HAVING COUNT(i) = 5'
        );

        return $query->getResult();
    }
}
