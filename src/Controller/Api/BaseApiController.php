<?php

namespace App\Controller\Api;

use App\Entity\IEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Traits\JsonAppendTrait;

class BaseApiController extends AbstractController
{
    use JsonAppendTrait;

    protected function isTitleDuplicate(IEntity $base, IEntity $comparison): bool
    {
        if ($comparison instanceof $base) {
            if ($comparison->getTitle() === $comparison->getTitle()) {
                return true;
            }
        }
        return false;
    }

    protected function arrayContainsTitleDuplicate(array $array, IEntity $comparison): bool
    {
        foreach ($array as $item) {
            if ($this->isTitleDuplicate($comparison, $item)) {
                return true;
            }
        }
        return false;
    }

    // check for logical XOR
    protected function removeConjunctItemsBasedOnTitleInTwoArrays(array $base, array $newItems): array
    {
        $nonConjunctItems = [];
        if (!empty($base) && !empty($newItems)) {
            foreach ($newItems as $newItem) {
                if (!$this->arrayContainsTitleDuplicate($base, $newItem)) {
                    $nonConjunctItems += [$newItem];
                }
            }
            return $nonConjunctItems;
        }
        return $base;
    }
}