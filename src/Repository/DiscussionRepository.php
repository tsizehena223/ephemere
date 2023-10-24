<?php

namespace App\Repository;

use App\Entity\Discussion;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discussion>
 *
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discussion::class);
    }

    public function findBySenderOrRecipient(?User $user)
    {
        return $this->createQueryBuilder("d")
            ->select("d.id", "d.content", "d.createdAt", "sender.id as senderId", "recipient.id as recipientId")
            ->leftJoin("d.sender", "sender")
            ->leftJoin("d.recipient", "recipient")
            ->where("sender.id = :sender OR recipient.id = :sender")
            ->setParameter('sender', $user)
            ->orderBy("d.createdAt", "DESC")
            ->getQuery()
            ->getResult();
    }
}
