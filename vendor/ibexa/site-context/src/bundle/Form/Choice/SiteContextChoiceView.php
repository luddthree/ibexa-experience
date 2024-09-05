<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteContext\Form\Choice;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;

final class SiteContextChoiceView extends ChoiceView
{
    public ?string $thumbnail;

    public Language $language;

    public static function compare(self $a, self $b): int
    {
        if (!is_string($a->label) || !is_string($b->label)) {
            return 0;
        }

        $result = strcmp($a->label, $b->label);
        if ($result === 0) {
            $result = strcmp($a->language->name, $b->language->name);
        }

        return $result;
    }

    public static function decorate(ChoiceView $view): self
    {
        return new self(
            $view->data,
            $view->value,
            $view->label,
            $view->attr,
            $view->labelTranslationParameters
        );
    }
}
