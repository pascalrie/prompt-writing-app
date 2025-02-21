<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Folder;
use App\Entity\Note;
use App\Entity\Prompt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class NoteFixtures extends Fixture implements DependentFixtureInterface
{
    public const NOTE_REFERENCE = 'note_fixture';

    public function load(ObjectManager $manager)
    {
        $note = new Note();
        $note->addContent('This is my first note.');
        $note->setTitle('First note');
        $note->addTag($this->getReference(TagFixtures::TAG_REFERENCE));
        $note->setFolder($this->getReference(FolderFixtures::FOLDER_REFERENCE, Folder::class));
        $note->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE, Category::class));
        $note->setPrompt($this->getReference(PromptFixtures::PROMPT_REFERENCE, Prompt::class));

        $manager->persist($note);
        $manager->flush();

        $this->addReference(self::NOTE_REFERENCE, $note);
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
            FolderFixtures::class,
            CategoryFixtures::class,
            PromptFixtures::class,
        ];
    }
}