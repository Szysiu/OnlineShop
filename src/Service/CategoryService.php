<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class CategoryService
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager=$entityManager;
    }

    public function createNewCategory(Item $item,array $data):Category
    {
        $category=$this->entityManager->getRepository(Category::class)->findOneBy(['id'=>$data['category']]);
        if(!$category) {
            throw new NotFoundResourceException();
        }
        $category->addItem($item);

        return $category;
    }

    public function findAllCategories():array
    {

        $categories = $this->entityManager->getRepository(Category::class)->findAll();

        if(!$categories) {
            throw new NotFoundResourceException();
        }

        return $categories;
    }
}