<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ReferenceDefinition\Language;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Reference\Reference;
use Webmozart\Assert\Assert;

final class LanguageIdResolver implements LanguageResolverInterface
{
    public static function getHandledType(): string
    {
        return 'language_id';
    }

    public function resolve(ReferenceDefinition $referenceDefinition, Language $language): Reference
    {
        $name = $referenceDefinition->getName();
        Assert::notNull(
            $language->id,
            'Language object does not contain an ID. Make sure to reload language object if persisted.'
        );

        return Reference::create($name, $language->id);
    }
}
