<?php

namespace App\Repository\Factory\ConcreteCreators;

use App\Repository\Factory\IRepository;
use App\Repository\Factory\RepositoryCreator;
use App\Repository\NoteRepository;
use Doctrine\Persistence\ManagerRegistry;

class NoteCreator extends RepositoryCreator
{
    public static function manufactureRepository(ManagerRegistry $managerRegistry): IRepository
    {
        return new NoteRepository($managerRegistry);
    }
}