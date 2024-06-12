<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Traits\CreateReadDeleteTrait;
use App\Entity\PostType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class PostTypeCrudController extends AbstractCrudController
{
    use CreateReadDeleteTrait;

    public static function getEntityFqcn(): string
    {
        return PostType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
