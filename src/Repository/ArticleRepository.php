<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function projectNotVerified()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.updatedAt', 'a.situationTravail', 'a.domaine', 'a.updatedAt', 'a.fileName as file', 'user.name as author')
            ->leftJoin('a.author', 'user')
            ->andWhere('a.verifiedAdmin = :val')
            ->setParameter('val', 0)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function projectVerified()
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.updatedAt', 'a.situationTravail', 'a.domaine', 'a.updatedAt', 'a.fileName as file', 'user.name as author')
            ->leftJoin('a.author', 'user')
            ->andWhere('a.verifiedAdmin = :val')
            ->setParameter('val', 1)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function projectByDomaine($field)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.domaine = :val')
            ->setParameter('val', $field)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Article[] Returns an array of Article objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Article
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
