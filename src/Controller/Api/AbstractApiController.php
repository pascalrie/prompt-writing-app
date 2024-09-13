<?php

namespace App\Controller\Api;

use App\Repository\Factory\RepositoryCreator;
use Doctrine\ORM\EntityManagerInterface;
use http\Exception\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Traits\JsonResponseTrait;

class AbstractApiController extends AbstractController
{
    use JsonResponseTrait;

    protected RepositoryCreator $repositoryFactory;

    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RepositoryCreator $repositoryFactory)
    {
        $this->entityManager = $entityManager;
        $this->repositoryFactory = $repositoryFactory;
    }

    protected function responseForEachImplementedController(string $className)
    {
        // TODO: implement, with classNames as strings for message-handling, possible?
        dd($className);
    }

    protected function findDisjunctItemsInTwoArraysBasedOnTitle(array $base, array $comparison): array
    {
        // TODO: implement
        return [];
    }

    protected function isTitleDuplicate($base, $comparison): bool
    {
        if ($comparison instanceof $base) {
            if ($comparison->getTitle() === $comparison->getTitle()) {
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
        if (is_array($base)) {
            throw new InvalidArgumentException('Base-argument should be an entity, not an array');
        }
        if (is_array($comparison)) {
            throw new InvalidArgumentException('Comparison-argument should be an entity, not an array');
        }

        if (null === $base) {
            throw new \InvalidArgumentException('Base cannot be null.');
        }

        if (null === $comparison) {
            throw new \InvalidArgumentException('Comparison cannot be null.');
        }

        $classOfBase = get_class($base);
        $classOfComparison = get_class($comparison);

        if (!$classOfComparison instanceof $classOfBase) {
            throw new \InvalidArgumentException('Class of the first entity is not the same as the class of the entity to compare.');
        }

        $getFunctionForBaseProperty = $this->getGetFunctionOfProperty($basePropertyKey);
        $getFunctionForComparisonProperty = $this->getGetFunctionOfProperty($comparisonPropertyKey);

        $baseRepository = $this->repositoryFactory->getRepository($this->entityManager, $classOfBase);
        $comparisonRepository = $this->repositoryFactory->getRepository($this->entityManager, $classOfComparison);

        $basePropertyValue = $base->$getFunctionForBaseProperty();
        $comparisonPropertyValue = $comparison->$getFunctionForComparisonProperty();

        $baseEntity = $baseRepository->findBy([$basePropertyKey => $basePropertyValue]);
        $comparisonEntity = $comparisonRepository->findBy([$comparisonPropertyKey => $comparisonPropertyValue]);

        if ($baseEntity === $comparisonEntity) {
            return true;
        }

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
                if ($this->isTitleDuplicate($item, $comparison)) {
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
     * based on a property of an entity (criteria): find disjunct-objects in two arrays
     * @param array $base of items
     * @param array $itemsToCompare of items
     * @param string $basePropertyKey
     * @param string $comparisonPropertyKey
     * @return array of objects (e.g. array of Tags)
     */

    protected
    function findNonDuplicateObjectsInTwoArraysWithVariableCriteria
    (array  $base, array $itemsToCompare, string $basePropertyKey = 'title',
     string $comparisonPropertyKey = 'title'): array
    {
        $nonConjunctItems = [];
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
                $nonConjunctItems += [$newItem];
            }
            return $nonConjunctItems;
        }
        return $base;
    }

    private
    function getGetFunctionOfProperty(string $key): string
    {
        return 'get' . strtoupper($key);
    }
}