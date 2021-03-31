<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use App\Helper\FilePath;
use App\Helper\StringHelper;
use Minwork\Helper\Arr;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class TranslationFindMissingCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:find-missing';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        parent::__construct();

        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->setDescription('find missing translations');
        $this->addArgument('sourceFile', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceFile = $input->getArgument('sourceFile');

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

        $missingTranslationsCount = 0;
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
                $io->writeln($grepResultRow);
                ++$missingTranslationsCount;
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

            $io->writeln($translationKeyFromGrep);
            ++$missingTranslationsCount;
        }

        if ($missingTranslationsCount > 0) {
            $io->error('MISSING TRANSLATIONS FOUND');
        } else {
            $io->success('no missing translations');
        }

        return ConsoleReturnEnum::SUCCESS;
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
