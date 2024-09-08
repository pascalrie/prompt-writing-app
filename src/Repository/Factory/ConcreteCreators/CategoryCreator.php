<?php

namespace App\Repository\Factory\ConcreteCreators;

use App\Repository\CategoryRepository;
use App\Repository\Factory\IRepository;
use App\Repository\Factory\RepositoryCreator;
use Doctrine\Persistence\ManagerRegistry;

class CategoryCreator extends RepositoryCreator
{
    public static function manufactureRepository(ManagerRegistry $managerRegistry): IRepository
    {
        return new CategoryRepository($managerRegistry);
    }
}