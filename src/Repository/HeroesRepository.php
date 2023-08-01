<?php

namespace App\Repository;

use App\Entity\Heroes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Heroes>
 *
 * @method Heroes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Heroes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Heroes[]    findAll()
 * @method Heroes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HeroesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Heroes::class);
    }

//    /**
//     * @return Heroes[] Returns an array of Heroes objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Heroes
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByParams(string $name) : array{
        $qb = $this->createQueryBuilder('h');
        $query = $qb
            ->where($qb->expr()->like('h.nombre', ":nombre"))
            ->setParameter("nombre", "%$name%")
            ->getQuery();
        return $query->execute();
    }

}
