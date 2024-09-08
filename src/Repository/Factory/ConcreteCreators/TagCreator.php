<?php

namespace App\Repository\Factory\ConcreteCreators;

use App\Repository\Factory\IRepository;
use App\Repository\Factory\RepositoryCreator;
use App\Repository\TagRepository;
use Doctrine\Persistence\ManagerRegistry;

class TagCreator extends RepositoryCreator
{
    public static function manufactureRepository(ManagerRegistry $managerRegistry): IRepository
    {
        return new TagRepository($managerRegistry);
    }
}