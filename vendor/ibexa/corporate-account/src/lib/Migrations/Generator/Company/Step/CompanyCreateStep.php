<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Migrations\Generator\Company\Step;

use Ibexa\CorporateAccount\Migrations\Generator\Company\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepInterface;
use Ibexa\Migration\ValueObject\Step\ReferenceAwareStepTrait;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class CompanyCreateStep implements StepInterface, ReferenceAwareStepInterface
{
    use ReferenceAwareStepTrait;

    public CreateMetadata $metadata;

    /** @var \Ibexa\Migration\ValueObject\Content\Field[] */
    public array $fields;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(
        CreateMetadata $metadata,
        array $fields,
        array $references = []
    ) {
        Assert::allIsInstanceOf($fields, Field::class);

        $this->metadata = $metadata;
        $this->fields = $fields;
        $this->references = $references;
    }
}
