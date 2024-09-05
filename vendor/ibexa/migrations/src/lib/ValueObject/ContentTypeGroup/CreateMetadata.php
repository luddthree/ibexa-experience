<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\ContentTypeGroup;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeGroup;
use Webmozart\Assert\Assert;

final class CreateMetadata
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $creationDate;

    /** @var int|null */
    public $creatorId;

    /** @var bool */
    public $isSystem;

    private function __construct(
        string $identifier,
        string $creationDate,
        bool $isSystem,
        ?int $creatorId
    ) {
        $this->identifier = $identifier;
        $this->creationDate = $creationDate;
        $this->isSystem = $isSystem;
        $this->creatorId = $creatorId;
    }

    public static function create(ContentTypeGroup $contentTypeGroup): self
    {
        return new self(
            $contentTypeGroup->identifier,
            $contentTypeGroup->creationDate->format('c'),
            $contentTypeGroup->isSystem,
            $contentTypeGroup->creatorId,
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
            $data['creationDate'] ?? date('c'),
            $data['isSystem'] ?? false,
            $data['creatorId'] ?? null
        );
    }
}

class_alias(CreateMetadata::class, 'Ibexa\Platform\Migration\ValueObject\ContentTypeGroup\CreateMetadata');
