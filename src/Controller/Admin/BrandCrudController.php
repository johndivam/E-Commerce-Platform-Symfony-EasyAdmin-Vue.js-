<?php

namespace App\Controller\Admin;

use App\Entity\Brand;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Image;

class BrandCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Brand::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Brand')
            ->setEntityLabelInPlural('Brands')
            ->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id')
            ->hideOnForm();

        yield TextField::new('name');

        yield SlugField::new('slug')
            ->setTargetFieldName('name');

        yield ImageField::new('logo')
            ->setBasePath('uploads/brands')
            ->setUploadDir('public/uploads/brands')
            ->setUploadedFileNamePattern('[slug]-[randomhash].[extension]')
            ->setRequired(false)
            ->setFormTypeOption('constraints', [
                new Image([
                    'maxSize' => '300k', //500 KB
                    'maxWidth' => 600,
                    'maxHeight' => 600,
                ])
            ]);

        yield BooleanField::new('isActive');

        yield DateTimeField::new('createdAt')
            ->hideOnForm();

        yield DateTimeField::new('updatedAt')
            ->hideOnForm();
    }

    

}
