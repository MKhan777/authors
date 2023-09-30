<?php

namespace App\Form;

// src/Form/BookType.php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Assuming $options['author_choices'] is your original array
        $uniqueAuthorChoices = [];
        foreach ($options['author_choices'] as $authorId => $authorName) {
            // Create a unique key for each author by combining the author ID and name
            $uniqueKey = sprintf('%s - %s', $authorId, $authorName);
            $uniqueAuthorChoices[$uniqueKey] = $authorId;
        }

        $builder
            ->add('author', ChoiceType::class, [
                'choices' => array_flip($uniqueAuthorChoices), // Flip the array to use name as value
                'label' => 'Author',
                'choice_label' => fn($value, $key, $index) => $value,// Use the author's name as the label
                'choice_value' => function ($value) {
                    // Return the author's ID as the data value
                    return $value;
                },
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('release_date', DateType::class, [
                'label' => 'Release Date',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('isbn', TextType::class, [
                'label' => 'ISBN',
            ])
            ->add('format', TextType::class, [
                'label' => 'Format',
            ])
            ->add('number_of_pages', IntegerType::class, [
                'label' => 'Number of Pages',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'author_choices' => [], // Default value for author_choices
        ]);
    }
}
