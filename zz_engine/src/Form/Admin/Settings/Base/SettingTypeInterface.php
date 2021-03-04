<?php

declare(strict_types=1);

namespace App\Form\Admin\Settings\Base;

use Symfony\Component\Form\FormBuilderInterface;

interface SettingTypeInterface
{
    /**
     * @param array<array-key,mixed> $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void;
}
