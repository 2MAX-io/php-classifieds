<?php

declare(strict_types=1);

namespace App\Service\System\Text;

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
        $allowedCharacters = ' ';
        $allowedCharacters .= 'qwertyuiopasdfghjklzxcvbnm';
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
        $allowedCharacters .= $this->settingsService->getAllowedCharacters();
        $allowedCharacters .= \mb_strtoupper($this->settingsService->getAllowedCharacters());

        return \preg_replace('#[^'.\preg_quote($allowedCharacters).']+#', '', $text);
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

    private function calculateUpperCaseScore(string $text): float
    {
        $upperAllowedChars = \mb_strtoupper($this->settingsService->getAllowedCharacters());
        $uppercaseLetters = \preg_replace('#[^A-Z'. $upperAllowedChars .']#', '', $text);
        $uppercaseCount = \mb_strlen($uppercaseLetters);

        if ($uppercaseCount < 10) {
            return 0;
        }

        $textWithoutWhiteChars = \preg_replace('#['.\preg_quote(" \r\n~'!@#$%^&*_+-=?[]{}()<>.,:;|/\\\"").']#', '', $text);
        $lettersCount = \mb_strlen($textWithoutWhiteChars);

        return $uppercaseCount / $lettersCount;
    }
}
