<?php

namespace App\Entity;

interface IEntity
{
    public function getId(): ?int;

    public function __construct();

    public function jsonSerialize(): array;
}