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

    public function normalizeUserInput(string $text): string
    {
        $return = $text;
        $return = $this->removeNotAllowedCharacters($return);
        $return = $this->removeTooManyNewLines($return);
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
}
