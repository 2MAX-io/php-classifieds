<?php

declare(strict_types=1);

namespace App\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;
use Twig\Extension\RuntimeExtensionInterface;

class TwigForm implements RuntimeExtensionInterface
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(RequestStack $requestStack, Environment $twig)
    {
        $this->requestStack = $requestStack;
        $this->twig = $twig;
    }

    public function optionAttr(string $name, string $value): string
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        $selected = '';
        if ($request->get($name) === $value) {
            $selected = ' selected ';
        }

        $escapedValue = \twig_escape_filter($this->twig, $value);

        return <<<END
value="$escapedValue" $selected 
END;
    }
}
