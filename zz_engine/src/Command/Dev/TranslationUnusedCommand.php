<?php

declare(strict_types=1);

namespace App\Command\Dev;

use App\Enum\ConsoleReturnEnum;
use App\Helper\DateHelper;
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

class TranslationUnusedCommand extends Command
{
    protected static $defaultName = 'app:dev:translation:unused';

    /**
     * @var string[]
     */
    private static $ignoredTranslations = [
        "trans.We received request to change your account's email address to",
        'trans.enter here, all characters specific to your language, except for standard characters A-Z, 0-9, for example: ąĄßöИ, if needed',
    ];

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
        $this->setDescription('find unused translations');
        $this->addArgument('sourceFile', InputArgument::REQUIRED);
        $this->addOption('remove-unused');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $unusedCount = 0;
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
        foreach ($translations as $translationKey => $translationValue) {
            $translationKeySafe = $translationKey;
            $translationKeySafe = \escapeshellarg($translationKeySafe);
            if (empty(\trim($translationKeySafe, "'"))) {
                $this->logger->debug('skipped: `{translationKey}`', [
                    'translationKey' => $translationKey,
                ]);
                continue;
            }

            $grepCommand = "grep -r --fixed-strings --max-count=1 {$translationKeySafe} {$pathsStringSafe}";
            $grepReturn = \exec($grepCommand);
            if (empty($grepReturn)) {
                $isArrayOfTranslationOptions = StringHelper::match('~^trans\.[\w]+\.~', $translationKey);
                if ($isArrayOfTranslationOptions) {
                    $this->logger->debug('skipped: `{translationKey}`', [
                        'translationKey' => $translationKey,
                    ]);
                    continue;
                }

                if (\in_array($translationKey, static::$ignoredTranslations, true)) {
                    $this->logger->debug('skipped: `{translationKey}`', [
                        'translationKey' => $translationKey,
                    ]);
                    continue;
                }

                $io->writeln($translationKey);
                unset($translations[$translationKey]);
                ++$unusedCount;
            }
        }

        \uksort($translations, static function (string $a, string $b) {
            $aHasOnlySpecialCharacters = StringHelper::match('~^[\W]+$~', $a);
            $bHasOnlySpecialCharacters = StringHelper::match('~^[\W]+$~', $b);
            if ($aHasOnlySpecialCharacters) {
                return -1;
            }
            if ($bHasOnlySpecialCharacters) {
                return 1;
            }

            $aBeginsWithTrans = StringHelper::beginsWith($a, 'trans.');
            $bBeginWithTrans = StringHelper::beginsWith($b, 'trans.');
            $aIsArrayOfTranslationOptions = StringHelper::match('~^trans\.[\w]+\.~', $a);
            $bIsArrayOfTranslationOptions = StringHelper::match('~^trans\.[\w]+\.~', $b);

            if ($aBeginsWithTrans && $bBeginWithTrans && $aIsArrayOfTranslationOptions && !$bIsArrayOfTranslationOptions) {
                return -1;
            }
            if ($aBeginsWithTrans && $bBeginWithTrans && !$aIsArrayOfTranslationOptions && $bIsArrayOfTranslationOptions) {
                return 1;
            }
            if (!$aBeginsWithTrans && $bBeginWithTrans) {
                return -1;
            }
            if ($aBeginsWithTrans && !$bBeginWithTrans) {
                return 1;
            }

            return \strnatcasecmp($a, $b);
        });

        if ($input->getOption('remove-unused')) {
            \file_put_contents(
                \dirname($sourceFile).'/'.DateHelper::date('Y-m-d H:i:s_').\basename($sourceFile),
                Yaml::dump($translations)
            );
        }

        if (0 === $unusedCount) {
            $io->success('no unused found');
        } else {
            $io->error('unused translations found');
        }

        return ConsoleReturnEnum::SUCCESS;
    }
}
