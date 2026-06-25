<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\Image;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id')
            ->hideOnForm(),

            TextField::new('name')
                ->setLabel('Name')
                ->setHelp('Enter the full product name')
                ->setSortable(false),

            AssociationField::new('category')
                ->setLabel('Category')
                ->setSortable(false),

            AssociationField::new('brand')
                ->setLabel('Brand')
                ->setSortable(false),

            SlugField::new('slug')
                ->setTargetFieldName('name')
                ->setSortable(false),
                
            // SKU
            TextField::new('sku')
                ->setLabel('SKU')
                ->setSortable(false),

            ImageField::new('image')
                ->setBasePath('uploads/products')
                ->setUploadDir('public/uploads/products')
                ->setUploadedFileNamePattern('[uuid].[extension]')
                ->setRequired(false)
                ->setSortable(false),


            TextareaField::new('description')
                ->setLabel('Full Description')
                ->setNumOfRows(5)
                ->hideOnIndex()
                ->setSortable(false),

            TextareaField::new('shortDescription')
                ->setLabel('Short Description')
                ->setNumOfRows(3)
                ->hideOnIndex()
                ->setSortable(false),

            // Prices – decimal with 2 decimals
            NumberField::new('price')
                ->setLabel('Price')
                ->setNumDecimals(2),

            NumberField::new('oldPrice')
                ->setLabel('Old Price')
                ->setNumDecimals(2)
                ->setHelp('Leave empty if no sale')
                ->setSortable(false),

            // Stock
            IntegerField::new('stock')
                ->setLabel('Stock Quantity'),

            // Status – use a choice field with predefined options
            ChoiceField::new('status')
                ->setLabel('Status')
                ->setChoices([
                    'Available' => 'available',
                    'Out of Stock' => 'out_of_stock',
                    'Discontinued' => 'discontinued',
                ])
                ->renderAsBadges([
                    'available' => 'success',
                    'out_of_stock' => 'warning',
                    'discontinued' => 'danger',
                ]),

        ];
    }
    
}



