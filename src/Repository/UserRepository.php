<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @implements PasswordUpgraderInterface<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }


    public function findByProfEtud($currentId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1 OR u.profil = :p2')
            ->setParameters(['id' => $currentId, 'p1' => 'etudiant', 'p2' => 'professeur'])
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findByInvestisseur($currentId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1')
            ->setParameters(['id' => $currentId, 'p1' => 'investisseur'])
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findByEntreprise($currentId): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1')
            ->setParameters(['id' => $currentId, 'p1' => 'entreprise'])
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    public function findByNameAndProfEtud($currentId, $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere($this->createQueryBuilder("u")->expr()->like("u.name", "'%$name%'"))
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1 OR u.profil = :p2')
            ->setParameters(['id' => $currentId, 'p1' => 'etudiant', 'p2' => 'professeur'])
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByNameAndEntreprise($currentId, $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere($this->createQueryBuilder("u")->expr()->like("u.name", "'%$name%'"))
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1')
            ->setParameters(['id' => $currentId, 'p1' => 'entreprise'])
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByNameAndInvestisseur($currentId, $name): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere($this->createQueryBuilder("u")->expr()->like("u.name", "'%$name%'"))
            ->andWhere('u.id != :id')
            ->andWhere('u.profil = :p1')
            ->setParameters(['id' => $currentId, 'p1' => 'investisseur'])
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
