<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\ValueObject\Content;

use Ibexa\Contracts\Core\Repository\Values\ObjectState\ObjectState as APIObjectState;
use Webmozart\Assert\Assert;

final class ObjectState
{
    /** @var string */
    public $identifier;

    /** @var string */
    public $groupIdentifier;

    private function __construct(string $identifier, string $groupIdentifier)
    {
        $this->identifier = $identifier;
        $this->groupIdentifier = $groupIdentifier;
    }

    /**
     * @param array{
     *     identifier: string,
     *     groupIdentifier: string,
     * } $data
     */
    public static function createFromArray(array $data): self
    {
        Assert::keyExists($data, 'identifier');
        Assert::keyExists($data, 'groupIdentifier');

        return new self(
            $data['identifier'],
            $data['groupIdentifier'],
        );
    }

    public static function createFromValueObject(APIObjectState $objectState): self
    {
        return new self(
            $objectState->identifier,
            $objectState->getObjectStateGroup()->identifier,
        );
    }
}

class_alias(ObjectState::class, 'Ibexa\Platform\Migration\ValueObject\Content\ObjectState');
