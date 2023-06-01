<?php

namespace App\Form;

use App\Service\CategoryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AddItemType extends AbstractType
{
    private  CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image',FileType::class,[
                'label' => 'Zdjęcie',
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/*'],
                        'mimeTypesMessage' => 'Plik musi być obrazem'
                    ])
                ],

            ])
            ->add('name',TextType::class,[
                'label' => 'Nazwa przedmiotu',
                'required' => true,
            ])
            ->add('title',TextareaType::class,[
                'label' => 'Opis',
                'required' => true,
            ])
            ->add('price',IntegerType::class,[
                'label' => 'Cena'
            ])
            ->add('category',ChoiceType::class,[
                'label' => 'Kategoria',
                'choices' => $this->generateChoices()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    private function generateChoices(): array
    {
        $categories = $this->categoryService->findAllCategories();
        $choices = [];

        foreach ($categories as $category) {
            $choices[$category->getName()] = $category->getId();
        }

        return $choices;
    }
}
