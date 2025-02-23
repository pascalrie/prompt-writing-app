<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Tag::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @param Tag $entity
     * @param bool $flush
     * @return void
     */
    public function add(Tag $entity, bool $flush = true): void
    {
        $this->entityManager->persist($entity);

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param Tag $entity
     * @param bool $flush
     * @return void
     */
    public function remove(Tag $entity, bool $flush = true): void
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
     * @param Tag $tag
     * @return void
     */
    public function persist(Tag $tag): void
    {
        $this->entityManager->persist($tag);
    }
}
