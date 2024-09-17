<?php

namespace App\Service\Factory\ConcreteCreators;

use App\Repository\FolderRepository;
use App\Service\Factory\BaseServiceCreator;
use App\Service\FolderService;
use Doctrine\Persistence\ManagerRegistry;

class FolderServiceCreator extends BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry): FolderService
    {
        return new FolderService(new FolderRepository($registry));
    }
}