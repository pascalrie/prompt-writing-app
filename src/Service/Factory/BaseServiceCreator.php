<?php

namespace App\Service\Factory;

use Doctrine\Persistence\ManagerRegistry;

abstract class BaseServiceCreator
{
    public static function manufactureService(ManagerRegistry $registry)
    {
    }
}