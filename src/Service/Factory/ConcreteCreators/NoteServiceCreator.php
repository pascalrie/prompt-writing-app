<?php

namespace App\Service\Factory\ConcreteCreators;

use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Service\CategoryService;
use App\Service\Factory\BaseServiceCreator;
use App\Service\NoteService;
use Doctrine\Persistence\ManagerRegistry;

class NoteServiceCreator extends BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry): NoteService
    {
        return new NoteService(new NoteRepository($registry));
    }
}