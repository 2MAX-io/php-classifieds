<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Repository\PageRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placeholder' => 'trans.not required',
            'label' => 'trans.Select',
            'choices' => $this->getPages(),
            'choice_translation_domain' => false,
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getPages(): array
    {
        $pages = $this->pageRepository->findBy(['enabled' => true]);

        $returnList = [];
        foreach ($pages as $page) {
            $returnList[$page->getTitle()] = $page->getSlug();
        }

        return $returnList;
    }
}
