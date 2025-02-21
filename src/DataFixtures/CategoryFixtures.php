<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category_fixture';

    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setTitle('Category 1');
        // $category->addPrompt($this->getReference(PromptFixtures::PROMPT_REFERENCE, Prompt::class));

        $manager->persist($category);
        $manager->flush();

        $this->addReference(self::CATEGORY_REFERENCE, $category);
    }

/*    public function getDependencies(): array
    {
        return [
            PromptFixtures::class,
        ];
    }
*/
}