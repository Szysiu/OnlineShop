<?php

namespace App\Form;

use App\Service\CategoryService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormType extends AbstractType
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('phrase', TextType::class, [
                'label' => ' ',
                'required' => false,
                'attr' => [
                    'placeholder' => 'wyszukaj',
                    'class' => 'form-control',
                    'aria-describedby' => 'button-addon2'
                ]
            ])
            ->add('params', ChoiceType::class, [
                'label' => false,
                'choices' => $this->generateChoices(),
                'expanded' => true,
                'multiple' => true,
                'attr' => [
                    'class' => 'btn-check',
                    'autocomplete' => false
                ]
            ]);
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
