<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Note;
use App\Entity\Tag;
use App\Repository\CategoryRepository;
use App\Repository\NoteRepository;
use App\Repository\TagRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class NoteService implements IService
{
    protected NoteRepository $noteRepository;

    protected TagRepository $tagRepository;

    protected CategoryRepository $categoryRepository;

    /**
     * @param NoteRepository $noteRepository
     * @param TagRepository $tagRepository
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(NoteRepository     $noteRepository, TagRepository $tagRepository,
                                CategoryRepository $categoryRepository)
    {
        $this->noteRepository = $noteRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param string $title
     * @param string $content
     * @param ArrayCollection|null $tags
     * @param Category|null $category
     * @return Note
     */
    public function create(string $title = "", string $content = "", ArrayCollection $tags = null, Category $category = null): Note
    {
        $note = new Note();

        if (null === $title) {
            $title = substr($content, 0, 15) . '...';
        }

        $note->setTitle($title);

        $note->setContent($content);

        if (!$tags->isEmpty()) {
            $tagsForForeach = $tags->toArray();
            foreach ($tagsForForeach as $tag) {
                if ($tag instanceof Tag) {
                    $note->addTag($tag);
                }
            }
        }

        if (null !== $category) {
            $note->setCategory($category);
        }

        $this->noteRepository->add($note, true);
        return $note;
    }

    /**
     * @param int $noteId
     * @param string $newTitle
     * @param string $content
     * @param bool $contentShouldBeAdded
     * @param bool $contentShouldBeRemoved
     * @param array|null $newTags
     * @param string|null $newCategoryTitle
     * @return void
     */
    public function update(int  $noteId, string $newTitle = "", string $content = "", bool $contentShouldBeAdded = false,
                           bool $contentShouldBeRemoved = false, array $newTags = [], ?string $newCategoryTitle = ""): Note
    {
        $noteFromDb = $this->noteRepository->findBy(['id' => $noteId])[0];

        if ($contentShouldBeAdded && $content !== "" && !$contentShouldBeRemoved) {
            $noteFromDb->addContent($content);
        }

        if (!$contentShouldBeAdded && $content !== "" && !$contentShouldBeRemoved) {
            $noteFromDb->setContent($content);
        }

        if ($contentShouldBeRemoved) {
            $noteFromDb->setContent("");
        }

        if ("" !== $newTitle) {
            $noteFromDb->setTitle($newTitle);
        }

        if ([] !== $newTags) {
            foreach ($newTags as $tag) {
                $tagFromDb = $this->tagRepository->findBy(['title' => $tag])[0];
                if ($tagFromDb instanceof Tag) {
                    $noteFromDb->addTag($tag);
                }
            }
        }

        if ("" !== $newCategoryTitle) {
            $newCategoryFromDb = $this->categoryRepository->findBy(['title' => $newCategoryTitle])[0];
            $noteFromDb->setCategory($newCategoryFromDb);
        }

        $noteFromDb->setUpdatedAt(new DateTime('NOW'));
        $this->noteRepository->flush();

        return $noteFromDb;
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->noteRepository->findAll();
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $note = $this->noteRepository->findBy(['id' => $id])[0];
        $this->noteRepository->remove($note, true);
    }

    /**
     * @param int $id
     * @return Note|null
     */
    public function show(int $id): ?Note
    {
        $notes = $this->noteRepository->findBy(['id' => $id]);
        if (empty($notes)) {
            return null;
        }

        return $notes[0];
    }

    /**
     * @param string $criteria
     * @param $argument
     * @return Note|null
     */
    public function showBy(string $criteria, $argument): ?Note
    {
        $notes = $this->noteRepository->findBy([$criteria => $argument]);
        if (empty($notes)) {
            return null;
        }
        return $notes[0];
    }

    /**
     * @param Note|null $note
     * @param Tag|null $tag
     * @return void
     */
    public function removeTagFromNote(?Note $note, ?Tag $tag)
    {
        $note->removeTag($tag);
        $this->noteRepository->flush();
    }
}