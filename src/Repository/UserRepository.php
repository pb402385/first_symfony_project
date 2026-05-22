<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, User::class);
    }


    public function paginateUsersNoBundle(int $page, int $limit): Paginator
    {

        return new Paginator($this
            ->createQueryBuilder('u')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->setHint(Paginator::HINT_ENABLE_DISTINCT, false)
        );
    }

    public function paginateUsers(int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('u'),
            $page,
            $limit,
        );
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByBirthdateSuperiorAt($value): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.bornAt > :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /**
     * @return User[] Returns an array of User objects
     */
    public function findByCountry($value): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.country = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return User[] Returns an array of User objects with Collection of all his attached documents
     */
    public function findUserAndDocumentsByUserID($value): array
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.documents', 'd')
            ->addSelect('d')                    // Fetch Join
            ->where('u.id = :id')
            ->setParameter('id', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findUserAndDocumentsAndNotesByUserID($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.documents', 'd')
            ->leftJoin('d.notes', 'n')           // Jointure sur les notes des documents
            ->addSelect('d')                     // Charge les documents
            ->addSelect('n')                     // Charge les notes
            ->where('u.id = :id')
            ->setParameter('id', $value)
            ->orderBy('d.createdAt', 'DESC')     // Optionnel : trier les documents
            ->getQuery()
            ->getOneOrNullResult();             // Retourne un seul User (ou null)
    }

    public function paginateUsersWithSearchTerm(
        int $page = 1,
        int $limit = 10,
        string $sort = 'u.createdAt',
        string $direction = 'DESC',
        ?string $search = null
    ): PaginationInterface
    {
        $qb = $this->createQueryBuilder('u')
            ->addSelect('u')
            ->groupBy('u.id');

        // ==================== RECHERCHE TEXTE ====================
        // 4 champs utiles à rehercher: nom du document, nom de l'user, email de l'user ou la catégorie
        if ($search) {
            $search = trim($search);
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('u.email', ':search'),
                    $qb->expr()->like('u.name', ':search'),
                    $qb->expr()->like('u.country', ':search')
                )
            )
                ->setParameter('search', '%' . $search . '%');
        }

        // ==================== TRI ====================
        $qb->orderBy($sort, $direction);

        return $this->paginator->paginate($qb, $page, $limit);
    }


    public function findSystemUser(): ?User
    {
        return $this->findOneBy(['email' => 'noreply@docshare.fr']);
    }

}
