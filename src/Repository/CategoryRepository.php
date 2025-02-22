<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository implements IRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
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
        $this->getEntityManager()->remove($category);

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
        $this->getEntityManager()->persist($category);
    }

    /**
     * @return void
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}

