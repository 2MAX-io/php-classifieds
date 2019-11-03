<?php

declare(strict_types=1);

namespace App\Service\System\Text;

use App\Helper\Str;
use App\Service\Setting\SettingsService;

class TextService
{
    /**
     * @var SettingsService
     */
    private $settingsService;

    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    public function normalizeUserInput(?string $text): ?string
    {
        if ($text === null) {
            return null;
        }

        $return = $text;
        $return = $this->removeNotAllowedCharacters($return);
        $return = $this->removeTooManyNewLines($return);
        $return = $this->forceLowerCaseIfAllCaps($return);
        $return = \trim($return);

        return $return;
    }

    public function removeNotAllowedCharacters(string $text): string
    {
        if (!$this->settingsService->getAllowedCharactersEnabled()) {
            return $text;
        }

        $allowedCharacters = static::getAllowedCharacters();
        $allowedCharacters .= $this->settingsService->getAllowedCharacters();
        $allowedCharacters .= \mb_strtoupper($this->settingsService->getAllowedCharacters());

        return \preg_replace('~[^'.\preg_quote($allowedCharacters, '~').']+~', '', $text);
    }

    public static function getAllowedCharacters(): string
    {
        $allowedCharacters = ' ';
        /** @noinspection SpellCheckingInspection */
        $allowedCharacters .= 'qwertyuiopasdfghjklzxcvbnm';
        /** @noinspection SpellCheckingInspection */
        $allowedCharacters .= 'QWERTYUIOPASDFGHJKLZXCVBNM';
        $allowedCharacters .= '1234567890';
        $allowedCharacters .= '~!@#$%^&*_+-=?';
        $allowedCharacters .= '[]{}()<>';
        $allowedCharacters .= '.,:;|';
        $allowedCharacters .= '\'';
        $allowedCharacters .= '"';
        $allowedCharacters .= '\\'; // character: \
        $allowedCharacters .= '/';
        $allowedCharacters .= "\n\r";

        return $allowedCharacters;
    }

    public function removeTooManyNewLines(string $text, int $threshold = 2): string
    {
        return \preg_replace('#(\r?\n){'.($threshold+2).',}#', "\r\n\r\n", $text);
    }

    public function forceLowerCaseIfAllCaps(string $text): string
    {
        $return = $text;

        if ($this->calculateUpperCaseScore($return) > 0.3) {
            return \mb_strtolower($return);
        }

        return $return;
    }

    public function removeWordsFromTitle(string $string): string
    {
        $wordsToRemove = \explode("\n", $this->settingsService->getSettingsDto()->getWordsToRemoveFromTitle());
        $wordsToRemove = \array_map(static function(string $element) {
            if (\mb_strlen($element) < 2) {
                return false;
            }

            return \trim($element);
        }, $wordsToRemove);

        return \trim(Str::replaceIgnoreCase($string, $wordsToRemove, ''));
    }

    private function calculateUpperCaseScore(string $text): float
    {
        $upperAllowedChars = \mb_strtoupper($this->settingsService->getAllowedCharacters());
        $uppercaseLetters = \preg_replace('#[^A-Z'. $upperAllowedChars .']#', '', $text);
        $uppercaseCount = \mb_strlen($uppercaseLetters);

        if ($uppercaseCount < 10) {
            return 0;
        }

        $textWithoutWhiteChars = \preg_replace('~['.\preg_quote(" \r\n~'!@#$%^&*_+-=?[]{}()<>.,:;|/\\\"", '~').']~', '', $text);
        $lettersCount = \mb_strlen($textWithoutWhiteChars);

        return $uppercaseCount / $lettersCount;
    }
}
