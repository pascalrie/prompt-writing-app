<?php

namespace App\Controller\Admin;

use App\Entity\Prompt;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PromptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Prompt::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnDetail()->onlyOnIndex(),
            TextField::new('title'),
            AssociationField::new('category')
                ->setCrudController(CategoryCrudController::class)
                ->setFormTypeOptions(['choice_label' => 'title']),
            AssociationField::new('notes'),
        ];
    }
}
