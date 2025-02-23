<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    protected EntityManagerInterface $entityManager;

    /**
     * @param ManagerRegistry $registry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Category::class);

        $this->entityManager = $entityManager;
    }

    /**
     * @param Category $category
     * @param bool $flush
     * @return Category
     */
    public function add(Category $category, bool $flush = true): Category
    {
        $this->persist($category);

        if ($flush) {
            $this->flush();
        }

        return $category;
    }

    /**
     * @param Category $category
     * @param bool $flush
     * @return void
     */
    public function remove(Category $category, bool $flush = true): void
    {
        $this->entityManager->remove($category);

        if ($flush) {
            $this->flush();
        }
    }

    /**
     * @param Category $category
     * @return void
     */
    public function persist(Category $category): void
    {
        $this->entityManager->persist($category);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->entityManager->flush();
    }
}

