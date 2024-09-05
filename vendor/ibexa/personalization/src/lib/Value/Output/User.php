<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Output;

use Webmozart\Assert\Assert;

class User
{
    /** \Ibexa\Personalization\Value\Output\Attribute[] */
    private $attributes = [];

    /** @var string */
    private $userId;

    /**
     * @param \Ibexa\Personalization\Value\Output\Attribute[] $attributes
     */
    public function __construct(string $userId, array $attributes = [])
    {
        Assert::allIsInstanceOf($attributes, Attribute::class);

        $this->userId = $userId;
        $this->attributes = $attributes;
    }

    /**
     * @return \Ibexa\Personalization\Value\Output\Attribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}

class_alias(User::class, 'EzSystems\EzRecommendationClient\Value\Output\User');
