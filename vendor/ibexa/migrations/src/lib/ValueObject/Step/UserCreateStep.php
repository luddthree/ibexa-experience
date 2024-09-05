<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\User\CreateMetadata;

final class UserCreateStep implements StepInterface, ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\User\CreateMetadata */
    public $metadata;

    /** @var string[] */
    public $groups;

    /** @var \Ibexa\Migration\ValueObject\Content\Field[] */
    public $fields;

    /**
     * @param string[] $groups
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(CreateMetadata $metadata, array $groups, array $fields, array $references = [])
    {
        $this->metadata = $metadata;
        $this->groups = $groups;
        $this->fields = $fields;
        $this->references = $references;
    }
}

class_alias(UserCreateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\UserCreateStep');
