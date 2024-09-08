<?php

namespace App\Repository\Factory\ConcreteCreators;

use App\Repository\Factory\IRepository;
use App\Repository\Factory\RepositoryCreator;
use App\Repository\FolderRepository;
use Doctrine\Persistence\ManagerRegistry;

class FolderCreator extends RepositoryCreator
{
    public static function manufactureRepository(ManagerRegistry $managerRegistry): IRepository
    {
        return new FolderRepository($managerRegistry);
    }
}