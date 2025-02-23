<?php

namespace App\Service;

interface IService
{
    public function list();

    public function show(int $id);

    public function delete(int $id);
}