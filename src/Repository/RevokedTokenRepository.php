<?php

namespace App\Repository;

use App\Entity\RevokedToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RevokedToken>
 */
class RevokedTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RevokedToken::class);
    }

    public function isRevoked(string $token): bool
    {
        return $this->createQueryBuilder('r')
                ->where('r.token = :token')
                ->andWhere('r.expiresAt > :now')
                ->setParameter('token', $token)
                ->setParameter('now', new \DateTimeImmutable())
                ->getQuery()
                ->getOneOrNullResult() !== null;
    }

    public function revoke(string $token, \DateTimeImmutable $expiresAt): void
    {
        $revoked = new RevokedToken();
        $revoked->setToken($token);
        $revoked->setExpiresAt($expiresAt);

        $this->getEntityManager()->persist($revoked);
        $this->getEntityManager()->flush();
    }

    //    /**
    //     * @return RevokedToken[] Returns an array of RevokedToken objects
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

    //    public function findOneBySomeField($value): ?RevokedToken
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
