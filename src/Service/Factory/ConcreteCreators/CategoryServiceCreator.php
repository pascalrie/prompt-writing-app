<?php

namespace App\Service\Factory\ConcreteCreators;

use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\Service\Factory\BaseServiceCreator;
use Doctrine\Persistence\ManagerRegistry;

class CategoryServiceCreator extends BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry): CategoryService
    {
        return new CategoryService(new CategoryRepository($registry));
    }
}