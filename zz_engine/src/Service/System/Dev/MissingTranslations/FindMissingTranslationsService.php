<?php

declare(strict_types=1);

namespace App\Service\System\Dev\MissingTranslations;

use App\Helper\FilePath;
use App\Helper\StringHelper;
use Minwork\Helper\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class FindMissingTranslationsService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return string[]
     */
    public function findMissingTranslations(string $sourceFile): array
    {
        $missingTranslations = [];
        $paths = [];
        $paths[] = Path::canonicalize(FilePath::getEngineDir().'/src');
        $paths[] = Path::canonicalize(FilePath::getEngineDir().'/assets');
        $paths[] = Path::canonicalize(FilePath::getEngineDir().'/templates');
        $paths = \array_map('\escapeshellarg', $paths);
        $pathsStringSafe = \implode(' ', $paths);

        $translations = Yaml::parseFile($sourceFile);
        $translations = Arr::unpack($translations);
        $translations = \array_keys($translations);

        $grepCommand = "grep -r --fixed-strings 'trans.' {$pathsStringSafe}";
        $grepOutputArray = [];
        \exec($grepCommand, $grepOutputArray);

        foreach ($grepOutputArray as $grepResultRow) {
            $pluralizationPrefix = '';
            if (\str_contains($grepResultRow, '{1}trans.')) {
                $pluralizationPrefix = '{1}';
            }
            if (\str_contains($grepResultRow, '{0}trans.')) {
                $pluralizationPrefix = '{0}';
            }
            $transPrefix = $pluralizationPrefix.'trans.';
            if (\str_contains($grepResultRow, "'".$transPrefix."'")) {
                continue;
            }
            if (\str_contains($grepResultRow, '"'.$transPrefix.'"')) {
                continue;
            }
            if (\str_contains($grepResultRow, '`'.$transPrefix.'`')) {
                continue;
            }
            $translationKeyFromGrep = StringHelper::matchSingle(/** @lang text */ '~(?P<return>trans\.[^\'\"\n]+)~', $grepResultRow);
            if (!$translationKeyFromGrep) {
                $missingTranslations[] = $grepResultRow;
                continue;
            }

            $isArrayOfTranslationOptions = StringHelper::match('~^trans\.[\w]+\.~', $translationKeyFromGrep);
            if ($isArrayOfTranslationOptions) {
                continue;
            }

            if (\in_array($translationKeyFromGrep, $translations, true)) {
                continue;
            }
            $this->logger->debug('translation key not found using regexp', [
                '$grepResultRow' => $grepResultRow,
            ]);

            $translationKeyFromGrep = $this->extractTranslationKeyWhenEscaping($grepResultRow);
            if ($translationKeyFromGrep && \in_array($translationKeyFromGrep, $translations, true)) {
                continue;
            }

            if ($translationKeyFromGrep) {
                $missingTranslations[] = $translationKeyFromGrep;
            }
        }

        return $missingTranslations;
    }

    private function extractTranslationKeyWhenEscaping(string $grepResultRow): ?string
    {
        $translationKey = '';
        $pluralizationPrefix = '';
        if (\str_contains($grepResultRow, '{1}trans.')) {
            $pluralizationPrefix = '{1}';
        }
        if (\str_contains($grepResultRow, '{0}trans.')) {
            $pluralizationPrefix = '{0}';
        }
        $transPrefix = $pluralizationPrefix.'trans.';
        $transPrefixPos = \strpos($grepResultRow, $transPrefix);
        if (false === $transPrefixPos) {
            throw new \RuntimeException('could not find translation prefix in: '.$grepResultRow);
        }
        $stringDelimiter = $grepResultRow[$transPrefixPos - 1] ?? null;
        if (!\in_array($stringDelimiter, ['"', "'", '`'])) {
            $stringDelimiter = null;
        }

        $substrFromTransPrefixPos = \substr($grepResultRow, $transPrefixPos);
        $substrFromTransPrefixPosLength = \mb_strlen($substrFromTransPrefixPos);
        $prevChar = null;
        for ($i = 0; $i < $substrFromTransPrefixPosLength; ++$i) {
            $char = $substrFromTransPrefixPos[$i];
            if ("\n" === $char) {
                break;
            }
            if ($char === $stringDelimiter && '\\' !== $prevChar) {
                break;
            }

            $translationKey .= $char;
            $prevChar = $char;
        }

        $translationKey = StringHelper::replaceMultiple($translationKey, [
            "\\'" => "'",
        ]);

        if ('' === $translationKey) {
            return null;
        }

        return $translationKey;
    }
}
