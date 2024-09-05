<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Step;

use Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection;
use Ibexa\Migration\ValueObject\ContentType\Matcher;
use Ibexa\Migration\ValueObject\ContentType\UpdateMetadata;
use Webmozart\Assert\Assert;

final class ContentTypeUpdateStep implements StepInterface, ActionsAwareStepInterface, UserContextAwareStepInterface
{
    use ActionsAwareStepTrait;

    /** @var \Ibexa\Migration\ValueObject\ContentType\UpdateMetadata */
    private $metadata;

    /** @var \Ibexa\Migration\ValueObject\ContentType\FieldDefinitionCollection */
    private $fields;

    /** @var \Ibexa\Migration\ValueObject\ContentType\Matcher */
    private $match;

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action[] $actions
     */
    public function __construct(
        UpdateMetadata $metadata,
        FieldDefinitionCollection $fields,
        Matcher $match,
        array $actions = []
    ) {
        Assert::allIsInstanceOf($actions, Action::class);

        $this->metadata = $metadata;
        $this->fields = $fields;
        $this->match = $match;
        $this->actions = $actions;
    }

    public function getMetadata(): UpdateMetadata
    {
        return $this->metadata;
    }

    public function getFields(): FieldDefinitionCollection
    {
        return $this->fields;
    }

    public function getMatch(): Matcher
    {
        return $this->match;
    }

    public function getUserId(): ?int
    {
        return $this->metadata->modifierId;
    }
}

class_alias(ContentTypeUpdateStep::class, 'Ibexa\Platform\Migration\ValueObject\Step\ContentTypeUpdateStep');
