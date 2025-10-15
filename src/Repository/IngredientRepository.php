<?php

namespace App\Repository;

use App\Entity\Ingredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<Ingredient>
 *
 * @method Ingredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ingredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ingredient[]    findAll()
 * @method Ingredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IngredientRepository extends ServiceEntityRepository
{
    private LoggerInterface $logger;
    
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, Ingredient::class);
        $this->logger = $logger;
    }

    //    /**
    //     * @return Ingredient[] Returns an array of Ingredient objects
    //     */
    //    public function findByExampleField($prix): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $prix)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($prix): ?Ingredient
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


    // Utilisation du QueryBuilder pour éffectuer des requêtes

    public function find_ingredient_tomate(): array
    {
        // On logge un message à chaque appel
        $this->logger->info('APPEL DE find_ingredient_tomate');
        return $this->createQueryBuilder('i')
            ->andWhere('i.nom LIKE :val')
            ->setParameter('val', '%tomate%')
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function find_ingredient_tomate_5(): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.prix > 5')
            ->andWhere('i.nom LIKE :val')
            ->setParameter('val', '%tomate%')
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function find_ingredient_tom5(): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.nom LIKE :val')
            ->setParameter('val', 'tomate%')
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function find_ingredient_by_price($prix): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.prix = :val')
            ->setParameter('val', $prix)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function find_ingredient_by_price_and_name($prix, $nom): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.prix = :prix')
            ->setParameter('prix', $prix)
            ->andWhere('i.nom = :nom')
            ->setParameter('nom', $nom)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    // Utilisation du QueryBuilder pour éffectuer des requêtes

    public function findAll_sql(): array
    {
        $entity_manag = $this->getEntityManager();

        $query = $entity_manag->createQuery(
            'SELECT i
            FROM App\Entity\Ingredient i
            ORDER BY i.id ASC
            '
        );

        return $query->getResult();
    }


}
