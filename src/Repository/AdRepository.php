<?php

namespace App\Repository;

use App\Entity\Ad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @return Ad[] Returns an array of Ad objects
     */

    public function findLast()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults(6)
            ->getQuery()
            ->getResult();
    }


    public function findByRegion($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.regionsId = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult();
    }
    
    public function findUserByIdArticle($value)
    {
        return $this->createQueryBuilder('a')
            ->select('a, u')
            ->innerJoin('a.author', 'u')
            ->andWhere(' a.id = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getScalarResult();
    }
}
