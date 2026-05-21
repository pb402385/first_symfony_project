<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Document>
 */
class DocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Document::class);
    }

    public function paginateDocuments(int $page, int $limit): PaginationInterface
    {

        return $this->paginator->paginate(
            $this->createQueryBuilder('d'),
            $page,
            $limit,
        );
    }


    public function paginateDocumentsWithAvgNote(int $page = 1, int $limit = 10, string $sort = 'd.createdAt', string $direction = 'DESC'): PaginationInterface
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.notes', 'n')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.user', 'u')
            ->addSelect('AVG(n.rating) as avgNote')   // Moyenne des notes
            ->addSelect('c')
            ->addSelect('u')
            ->groupBy('d.id')
            ->addGroupBy('c.id')
            ->addGroupBy('u.id');

        // Gestion du tri
        if ($sort === 'avgNote') {
            $qb->orderBy('avgNote', $direction);
        } else {
            $qb->orderBy($sort, $direction);
        }

        return $this->paginator->paginate($qb, $page, $limit);
    }

    public function paginateDocumentsWithAvgNoteAndSearchTerm(
        int $page = 1,
        int $limit = 10,
        string $sort = 'd.createdAt',
        string $direction = 'DESC',
        ?string $search = null
    ): PaginationInterface
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.notes', 'n')
            ->leftJoin('d.category', 'c')
            ->leftJoin('d.user', 'u')
            ->addSelect('AVG(n.rating) as avgNote')
            ->addSelect('c')
            ->addSelect('u')
            ->groupBy('d.id')
            ->addGroupBy('c.id')
            ->addGroupBy('u.id');

        // ==================== RECHERCHE TEXTE ====================
        // 4 champs utiles à rehercher: nom du document, nom de l'user, email de l'user ou la catégorie
        if ($search) {
            $search = trim($search);
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('d.title', ':search'),
                    $qb->expr()->like('u.email', ':search'),
                    $qb->expr()->like('u.name', ':search'),
                    $qb->expr()->like('c.label', ':search')
                )
            )
                ->setParameter('search', '%' . $search . '%');
        }

        // ==================== TRI ====================
        if ($sort === 'avgNote') {
            $qb->orderBy('avgNote', $direction);
        } elseif ($sort === 'title') {
            $qb->orderBy('d.title', $direction);
        } else {
            $qb->orderBy($sort, $direction);
        }

        return $this->paginator->paginate($qb, $page, $limit);
    }


    public function findWithCategory(int $id): ?Document
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.category', 'c')
            ->addSelect('c')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
