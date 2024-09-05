<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Reference;

use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Language\LanguageCodeResolver;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Language\LanguageIdResolver;
use Webmozart\Assert\Assert;

final class LanguageGenerator extends AbstractReferenceGenerator
{
    protected function getReferenceMetadata(): array
    {
        return [
            new ReferenceMetadata('ref_id', LanguageIdResolver::getHandledType()),
            new ReferenceMetadata('ref_code', LanguageCodeResolver::getHandledType()),
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Language $language
     *
     * @return \Ibexa\Migration\Generator\Reference\ReferenceDefinition[]
     */
    public function generate(ValueObject $language): array
    {
        Assert::isInstanceOf($language, Language::class);

        return $this->generateReferences((string)$language->id, 'language');
    }
}

class_alias(LanguageGenerator::class, 'Ibexa\Platform\Migration\Generator\Reference\LanguageGenerator');
