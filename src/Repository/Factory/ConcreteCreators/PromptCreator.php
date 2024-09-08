<?php

namespace App\Repository\Factory\ConcreteCreators;

use App\Repository\Factory\IRepository;
use App\Repository\Factory\RepositoryCreator;
use App\Repository\PromptRepository;
use Doctrine\Persistence\ManagerRegistry;

class PromptCreator extends RepositoryCreator
{
    public static function manufactureRepository(ManagerRegistry $managerRegistry): IRepository
    {
        return new PromptRepository($managerRegistry);
    }
}