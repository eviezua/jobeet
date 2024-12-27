<?php

namespace App\Controller\Admin;

use App\Entity\Affiliate;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class AffiliateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Affiliate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Affiliate')
            ->setEntityLabelInPlural('Affiliates')
            ->setSearchFields(
                [
                    'id',
                    'url',
                    'email',
                    'token',
                    'active',
                    'state',
                    'owner.id',
                ]
            )
            ->setDefaultSort(['createdAt' => 'DESC']);
    }
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('email'))
            ->add(TextFilter::new('token'))
            ->add(BooleanFilter::new('active'))
            ->add(TextFilter::new('url'))
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('url');
        yield EmailField::new('email');
        yield BooleanField::new('active');
    }
}
