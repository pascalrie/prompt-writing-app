<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public const TAG_REFERENCE = 'tag_fixture';

    public function load(ObjectManager $manager)
    {
        $tag = new Tag();
        $tag->setTitle('#exampletag');
        $manager->persist($tag);
        $manager->flush();
        $this->addReference(self::TAG_REFERENCE, $tag);
    }
}