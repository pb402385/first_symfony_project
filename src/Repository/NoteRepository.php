<?php
// src/Repository/NoteRepository.php
namespace App\Repository;

use App\Entity\Document;
use App\Entity\Note;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    // ====================== MÉTHODES DE BASE ======================

    public function findOneByUserAndDocument(User $user, Document $document): ?Note
    {
        return $this->createQueryBuilder('n')
            ->where('n.user = :user')
            ->andWhere('n.document = :document')
            ->setParameter('user', $user)
            ->setParameter('document', $document)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ====================== STATISTIQUES ======================

    /**
     * Note moyenne d'un document
     */
    public function getAverageRating(Document $document): ?float
    {
        $result = $this->createQueryBuilder('n')
            ->select('AVG(n.rating)')
            ->where('n.document = :document')
            ->setParameter('document', $document)
            ->getQuery()
            ->getSingleScalarResult();

        return $result !== null ? round((float)$result, 2) : null;
    }

    /**
     * Nombre total de notes pour un document
     */
    public function countNotesByDocument(Document $document): int
    {
        return $this->createQueryBuilder('n')
            ->select('COUNT(n.id)')
            ->where('n.document = :document')
            ->setParameter('document', $document)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Note moyenne + nombre de votes (en une seule requête)
     */
    public function getRatingStats(Document $document): array
    {
        return $this->createQueryBuilder('n')
            ->select('AVG(n.rating) as average', 'COUNT(n.id) as total')
            ->where('n.document = :document')
            ->setParameter('document', $document)
            ->getQuery()
            ->getOneOrNullResult();
    }

    // ====================== LISTES ======================

    /**
     * Toutes les notes d'un document (avec les utilisateurs)
     */
    public function findByDocumentWithUser(Document $document): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.user', 'u')
            ->addSelect('u')
            ->where('n.document = :document')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Toutes les notes d'un utilisateur
     */
    public function findByUserWithDocument(User $user): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.document', 'd')
            ->addSelect('d')
            ->where('n.user = :user')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Dernières notes laissées (pour un dashboard par exemple)
     */
    public function findLatestNotes(int $limit = 10): array
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.user', 'u')
            ->leftJoin('n.document', 'd')
            ->addSelect('u', 'd')
            ->orderBy('n.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
