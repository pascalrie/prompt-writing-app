<?php

namespace App\Repository;

use App\Entity\Prompt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Prompt>
 *
 * @method Prompt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Prompt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Prompt[]    findAll()
 * @method Prompt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PromptRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Prompt::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Prompt $entity
     * @param bool $flush
     * @return Prompt
     */
    public function add(Prompt $entity, bool $flush = true): Prompt
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
        return $entity;
    }

    /**
     * @param Prompt $promptForDeletion
     * @param bool $flush
     * @return void
     */
    public function remove(Prompt $promptForDeletion, bool $flush = true): void
    {
        $this->entityManager->remove($promptForDeletion);

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
     * @param Prompt $prompt
     * @return void
     */
    public function persist(Prompt $prompt): void
    {
        $this->entityManager->persist($prompt);
    }
}
