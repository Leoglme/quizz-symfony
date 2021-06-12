<?php

namespace App\Repository;

use App\Entity\CreateQuiz;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CreateQuiz|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreateQuiz|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreateQuiz[]    findAll()
 * @method CreateQuiz[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreateQuizRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CreateQuiz::class);
    }

    // /**
    //  * @return CreateQuiz[] Returns an array of CreateQuiz objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CreateQuiz
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
