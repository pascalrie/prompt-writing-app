<?php

namespace App\Controller\Admin;

use App\Entity\Note;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class NoteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Note::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail()->onlyOnIndex(),
            TextField::new('title'),
            TextEditorField::new('content'),
            DatetimeField::new('createdAt')->onlyOnIndex()->onlyOnDetail(),
            DatetimeField::new('updatedAt')->onlyOnDetail(),
            AssociationField::new('category')
                ->setCrudController(CategoryCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
            AssociationField::new('prompt')
                ->setCrudController(PromptCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
            AssociationField::new('tags')
                ->setCrudController(TagCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title', 'by_reference' => false]),
            AssociationField::new('folder')
                ->setCrudController(FolderCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
        ];
    }
}
