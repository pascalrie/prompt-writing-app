<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail()->onlyOnIndex(),
            TextField::new('title'),
            AssociationField::new('prompts')
                ->setCrudController(PromptCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
            AssociationField::new('notes')
                ->setCrudController(NoteCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
        ];
    }
}
