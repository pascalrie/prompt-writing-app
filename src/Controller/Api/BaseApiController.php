<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Traits\JsonAppendTrait;

class BaseApiController extends AbstractController
{
    use JsonAppendTrait;
}