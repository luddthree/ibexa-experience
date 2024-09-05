<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\UserGroup\Matcher;
use Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata;
use Webmozart\Assert\Assert;

final class UserGroupUpdateStep implements ActionsAwareStepInterface, ReferenceAwareStepInterface
{
    use ActionsAwareStepTrait;
    use ReferenceAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\UserGroup\UpdateMetadata|null */
    public $metadata;

    /** @var \Ibexa\Migration\ValueObject\Content\Field[] */
    public $fields;

    /** @var string[] */
    public $roles;

    /** @var \Ibexa\Migration\ValueObject\UserGroup\Matcher */
    public $match;

    /**
     * @param \Ibexa\Migration\ValueObject\Content\Field[] $fields
     * @param string[] $roles
     * @param \Ibexa\Migration\Generator\Reference\ReferenceDefinition[] $references
     */
    public function __construct(
        ?UpdateMetadata $metadata,
        Matcher $match,
        array $fields,
        array $roles,
        array $references = []
    ) {
        if (!empty($fields)) {
            Assert::allIsInstanceOf($fields, Field::class);
        }

        Assert::allIsInstanceOf($references, ReferenceDefinition::class);

        $this->metadata = $metadata;
        $this->match = $match;
        $this->fields = $fields;
        $this->roles = $roles;
        $this->references = $references;
    }
}

class_alias(UserGroupUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\UserGroupUpdateStep');
