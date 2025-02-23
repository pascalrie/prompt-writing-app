<?php

namespace App\Repository;

use App\Entity\Folder;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 *
 * @method Note|null find($id, $lockMode = null, $lockVersion = null)
 * @method Note|null findOneBy(array $criteria, array $orderBy = null)
 * @method Note[]    findAll()
 * @method Note[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoteRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Note::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Note $entity
     * @param bool $flush
     * @return void
     */
    public function add(Note $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Note $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Note $entity, bool $flush = true): void
    {
        $this->entityManager->remove($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->entityManager->flush();
    }

    /**
     * @param Note $note
     */
    public function persist(Note $note): void
    {
        $this->entityManager->persist($note);
    }
}
