<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Helper\FilePath;
use App\Helper\StringHelper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Languages;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LanguageIsoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', $this->getLanguageChoices());
        $resolver->setDefault('placeholder', 'trans.Select');
        $resolver->setDefault('choice_translation_domain', false);
    }

    /**
     * @return array<string,string>
     */
    public function getLanguageChoices(): array
    {
        $finder = new Finder();
        $finder->in(FilePath::getEngineDir().'/translations');
        $finder->ignoreUnreadableDirs();
        $finder->ignoreDotFiles(true);
        $finder->ignoreVCS(true);
        $finder->files();

        $languageNames = \array_keys(Languages::getNames());
        $translatedLanguages = [];
        $languagePattern = /** @lang text */ '~\.(?P<return>[\a-z]+)\.~';
        foreach ($finder->files()->getIterator() as $file) {
            $languageName = StringHelper::matchSingle($languagePattern, $file->getFilename());
            if (!$languageName) {
                continue;
            }
            if (!\in_array($languageName, $languageNames, true)) {
                continue;
            }
            $translatedLanguages[$languageName] = $languageName;
        }
        \asort($translatedLanguages);

        return $translatedLanguages;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
