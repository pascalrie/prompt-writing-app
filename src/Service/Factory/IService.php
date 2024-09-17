<?php

namespace App\Service\Factory;

use App\Repository\IRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;

interface IService
{
    public function list();

    public function show(int $id);

    public function delete(int $id);
}