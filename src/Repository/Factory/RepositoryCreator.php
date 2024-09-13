<?php

namespace App\Repository\Factory;

// This solution is a gist from GitHub (https://gist.github.com/docteurklein/9778800)
// in this class, there are adjustments, made by me in the getRepository()-function

namespace App\Repository\Factory;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;

class RepositoryCreator implements RepositoryFactory
{
    /** @var array $ids */
    private array $ids;
    private ContainerInterface $container;
    private RepositoryFactory $default;

    public function __construct(array $ids, ContainerInterface $container, RepositoryFactory $default)
    {
        $this->ids = $ids;
        $this->container = $container;
        $this->default = $default;
    }

    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        if (isset($this->ids[$entityName])) {
            return $this->container->get($this->ids[$entityName]);
        }
        return $this->default->getRepository($entityManager, $entityName);
    }
}
