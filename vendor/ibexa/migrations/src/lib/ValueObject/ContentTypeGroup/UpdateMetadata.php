<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Webmozart\Assert\Assert;

final class UpdateMetadata
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $modificationDate;

    /** @var int|null */
    public $modifierId;

    /** @var bool */
    public $isSystem;

    private function __construct(
        string $identifier,
        string $modificationDate,
        bool $isSystem,
        ?int $modifierId
    ) {
        $this->identifier = $identifier;
        $this->modificationDate = $modificationDate;
        $this->isSystem = $isSystem;
        $this->modifierId = $modifierId;
    }

    public static function create(ContentTypeGroup $contentTypeGroup): self
    {
        return new self(
            $contentTypeGroup->identifier,
            $contentTypeGroup->modificationDate->format('c'),
            $contentTypeGroup->isSystem,
            $contentTypeGroup->modifierId,
        );
    }

    /**
     * @param array<mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        Assert::keyExists($data, 'identifier');
        Assert::string($data['identifier']);

        return new self(
            $data['identifier'],
            $data['modificationDate'],
            $data['isSystem'] ?? false,
            $data['modifierId'] ?? null
        );
    }
}

class_alias(UpdateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ContentTypeGroup\UpdateMetadata');
