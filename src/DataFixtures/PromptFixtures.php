<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Prompt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PromptFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROMPT_REFERENCE = 'prompt_fixture';

    public function load(ObjectManager $manager): void
    {
        $prompt = new Prompt();
        $prompt->setTitle('Prompt 1?');
        $prompt->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE, Category::class));

        $manager->persist($prompt);
        $manager->flush();

        $this->addReference(self::PROMPT_REFERENCE, $prompt);
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class
        ];
    }
}