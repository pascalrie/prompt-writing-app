<?php

namespace App\Service\Factory\ConcreteCreators;

use App\Repository\CategoryRepository;
use App\Repository\PromptRepository;
use App\Service\CategoryService;
use App\Service\Factory\BaseServiceCreator;
use App\Service\PromptService;
use Doctrine\Persistence\ManagerRegistry;

class PromptServiceCreator extends BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry): PromptService
    {
        return new PromptService(new PromptRepository($registry));
    }
}
