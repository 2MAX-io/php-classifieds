<?php

declare(strict_types=1);

namespace App\Service\System\Text;

use App\Helper\StringHelper;
use App\Service\Setting\SettingsDto;

class TextService
{
    /**
     * @var SettingsDto
     */
    private $settingsDto;

    public function __construct(SettingsDto $settingsDto)
    {
        $this->settingsDto = $settingsDto;
    }

    public static function getAllowedCharacters(): string
    {
        $allowedCharacters = ' ';
        /* @noinspection SpellCheckingInspection */
        $allowedCharacters .= 'qwertyuiopasdfghjklzxcvbnm';
        /* @noinspection SpellCheckingInspection */
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

    public function normalizeUserInput(?string $text): ?string
    {
        if (null === $text) {
            return null;
        }

        $return = $text;
        $return = $this->removeNotAllowedCharacters($return);
        $return = $this->removeTooManyNewLines($return);
        $return = $this->forceLowerCaseIfAllCaps($return);

        return \trim($return);
    }

    public function removeNotAllowedCharacters(string $text): string
    {
        if (!$this->settingsDto->getAllowedCharactersEnabled()) {
            return $text;
        }

        $allowedCharacters = static::getAllowedCharacters();
        $allowedCharacters .= $this->settingsDto->getAllowedCharacters();
        $allowedCharacters .= \mb_strtoupper($this->settingsDto->getAllowedCharacters());

        return \preg_replace(
            '~[^'.\preg_quote($allowedCharacters, '~').']+~',
            '',
            $text,
        );
    }

    public function removeTooManyNewLines(string $text, int $threshold = 2): string
    {
        return \preg_replace('~(\r?\n){'.($threshold + 2).',}~', "\r\n\r\n", $text);
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
        $wordsToRemoveFromTitle = $this->settingsDto->getWordsToRemoveFromTitle();
        $wordsToRemoveExploded = \explode("\n", $wordsToRemoveFromTitle);
        $wordsToRemove = [];
        foreach ($wordsToRemoveExploded as $word) {
            if (\mb_strlen($word) < 2) {
                continue;
            }

            $wordsToRemove[] = \trim($word);
        }

        return \trim(StringHelper::replaceIgnoreCase($string, $wordsToRemove, ''));
    }

    private function calculateUpperCaseScore(string $text): float
    {
        $upperAllowedChars = \mb_strtoupper($this->settingsDto->getAllowedCharacters());
        $uppercaseLetters = \preg_replace('~[^A-Z'.$upperAllowedChars.']~', '', $text);
        $uppercaseCount = \mb_strlen($uppercaseLetters);

        if ($uppercaseCount < 10) {
            return 0;
        }

        $textWithoutWhiteChars = \preg_replace(
            '~['.\preg_quote(" \r\n~'!@#$%^&*_+-=?[]{}()<>.,:;|/\\\"", '~').']~',
            '',
            $text
        );
        $lettersCount = \mb_strlen($textWithoutWhiteChars);

        return $uppercaseCount / $lettersCount;
    }
}
