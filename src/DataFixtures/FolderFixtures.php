<?php

namespace App\DataFixtures;

use App\Entity\Folder;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FolderFixtures extends Fixture
{
    public const FOLDER_REFERENCE = 'folder_fixture';

    public function load(ObjectManager $manager)
    {
        $folder = new Folder();
        $folder->setTitle('Folder 1');

        $manager->persist($folder);
        $manager->flush();

        $this->addReference(self::FOLDER_REFERENCE, $folder);
    }
}