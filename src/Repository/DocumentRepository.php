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


    public function findWithCategory(int $id): ?Document
    {
        return $this->createQueryBuilder('d')
            ->leftJoin('d.category', 'c')
            ->addSelect('c')
            ->where('d.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

}
