<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Traits\JsonResponseTrait;

class BaseApiController extends AbstractController
{
    use JsonResponseTrait;

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function responseForEachImplementedController(string $className)
    {
        // TODO: implement, with classNames as strings for message-handling, possible?
        dd($className);
    }

    protected function findExclusivelyNewItemsInComparisonArrayBasedOnTitle(array $base, array $comparison): array
    {
        $results = [];

        if ($this->checkIfBothItemsOrObjectsAreNullOrEmpty($base, $comparison)) {
            return $base;
        }

        foreach ($base as $baseItem) {
            foreach ($comparison as $comparisonItem) {
                if (!$this->checkClassEquality($baseItem, $comparisonItem)) {
                    throw new InvalidArgumentException('Class of the first entity is not the same as the class of the entity to compare.');
                }
                if ($baseItem->getTitle() === $comparisonItem->getTitle()) {
                    $results += $baseItem;
                }
            }
        }
        return $results;
    }

    protected function isTitleOfObjectsOfSameClassDuplicate($base, $comparison): bool
    {
        if (get_class($comparison) === get_class($base)) {
            if ($base->getTitle() === $comparison->getTitle()) {
                return true;
            }
        }
        return false;
    }

    protected function isIdDuplicate($base, $comparison): bool
    {
        if ($comparison instanceof $base) {
            if ($comparison->getId() === $comparison->getId()) {
                return true;
            }
        }
        return false;
    }

    protected function isEntityBasedOnPropertyDuplicate($base, $comparison, string $basePropertyKey = 'title',
                                                        string $comparisonPropertyKey = 'title'): bool
    {
        dd("Currently not working method");
        if (is_array($base)) {
            throw new InvalidArgumentException('Base-argument should be an entity, not an array');
        }
        if (is_array($comparison)) {
            throw new InvalidArgumentException('Comparison-argument should be an entity, not an array');
        }

        if (null === $base || null === $comparison) {
            throw new InvalidArgumentException('Base-argument and/or Comparison-argument can\'t be null');
        }

        if (!$this->checkClassEquality($base, $comparison)) {
            throw new InvalidArgumentException('Base-argument-class and Comparison-class must be the same.');
        }

        $getFunctionForBaseProperty = $this->getGetFunctionOfProperty($basePropertyKey);
        $getFunctionForComparisonProperty = $this->getGetFunctionOfProperty($comparisonPropertyKey);

//        $baseRepository = $this->factoryHandler->decideAndReturnRepository(get_class($base), $this->entityManager);
//        $comparisonRepository = $this->factoryHandler->decideAndReturnRepository(get_class($comparison), $this->entityManager);

  //      $basePropertyValue = $base->$getFunctionForBaseProperty();
  //      $comparisonPropertyValue = $comparison->$getFunctionForComparisonProperty();

//        $baseEntity = $baseRepository->findBy([$basePropertyKey => $basePropertyValue]);
//        $comparisonEntity = $comparisonRepository->findBy([$comparisonPropertyKey => $comparisonPropertyValue]);

//        if ($baseEntity === $comparisonEntity) {
//            return true;
//        }

        return false;
    }

    /**
     * @param array $array
     * @param $comparison (is an item)
     * @param bool $compareWithTitle
     * @return bool
     */
    protected function arrayContainsDuplicate(array $array, $comparison, bool $compareWithTitle = false): bool
    {
        foreach ($array as $item) {
            if ($compareWithTitle) {
                if ($this->isTitleOfObjectsOfSameClassDuplicate($item, $comparison)) {
                    return true;
                }
                return false;
            }
            if ($this->isIdDuplicate($item, $comparison)) {
                return true;
            }
        }
        return false;
    }

    /**
     * based on a property of an entity (criteria): find exclusively new objects in comparison-array
     * @param array $base of items
     * @param array $itemsToCompare of items
     * @param string $basePropertyKey
     * @param string $comparisonPropertyKey
     * @return array of objects (e.g. array of Tags)
     */

    protected function findNonDuplicateObjectsInTwoArraysWithVariableCriteria
    (array  $base, array $itemsToCompare, string $basePropertyKey = 'title',
     string $comparisonPropertyKey = 'title'): array
    {
        $newItems = [];
        if (empty($base)) {
            throw new \InvalidArgumentException('Base-array can\'t be empty');
        }

        if (empty($itemsToCompare)) {
            throw new \InvalidArgumentException('itemsToCompare-array can\'t be empty');
        }

        foreach ($itemsToCompare as $newItem) {
            foreach ($base as $item) {
                if (get_class($item) !== get_class($newItem)) {
                    throw new InvalidArgumentException('The base-array and the newItems-array don\'t contain items of the same class');
                }
            }
        }

        foreach ($itemsToCompare as $newItem) {
            if (!$this->arrayContainsDuplicate($base, $newItem)) {
                $newItems += [$newItem];
            }
            return $newItems;
        }
        return $base;
    }

    private function getGetFunctionOfProperty(string $key): string
    {
        return 'get' . strtoupper($key);
    }

    private function checkClassEquality($first, $second): bool
    {
        if (get_class($first) !== get_class($second)) {
            return false;
        }
        return true;
    }

    private function checkIfBothItemsOrObjectsAreNullOrEmpty($first, $second): bool
    {
        if (null === $first && null === $second) {
            return true;
        }
        return false;
    }
}