<?php
namespace App\Service;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

class CategoryService
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function create(string $name) : Category
    {
        $category = new Category();
        $category->setName($name);

        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }
}
