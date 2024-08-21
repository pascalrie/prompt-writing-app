<?php

namespace App\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class ConversionUtil
{
    public static function convertCollectionIntoArrayCollection(Collection $collection): ArrayCollection
    {
        return new ArrayCollection($collection->toArray());
    }
}