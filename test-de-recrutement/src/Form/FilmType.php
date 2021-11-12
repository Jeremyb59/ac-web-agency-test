<?php

namespace App\Form;

use App\Entity\{ Film, Genre };
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;

class FilmType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Please upload a valid image document',
                    ]),
                ],
            ])
            ->add('genreFilm', ChoiceType::class, [
                'mapped' => false,
                'required' => false,
                'choices' => $this->getChoicesGenre(),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }

    private function getChoicesGenre()
    {
        $listGenre = [];
        $listGenreDatabase = $this->em
            ->getRepository(Genre::class)
            ->findAll();
        foreach ($listGenreDatabase as $genre) {
            $listGenre[$genre->getNom()] = $genre->getId();
        }

        return $listGenre;
    }
}
