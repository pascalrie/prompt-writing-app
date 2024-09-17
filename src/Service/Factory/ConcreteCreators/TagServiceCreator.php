<?php

namespace App\Service\Factory\ConcreteCreators;

use App\Repository\CategoryRepository;
use App\Repository\TagRepository;
use App\Service\CategoryService;
use App\Service\Factory\BaseServiceCreator;
use App\Service\TagService;
use Doctrine\Persistence\ManagerRegistry;

class TagServiceCreator extends BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry): TagService
    {
        return new TagService(new TagRepository($registry));
    }
}