<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\SiteFactory\Values\Query\Criterion;

class MatchName extends Matcher
{
    /** @var string */
    public $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        if ($name === null || $name === '') {
            throw new \InvalidArgumentException('Name pattern cannot be empty.');
        }

        $this->name = $name;
    }
}

class_alias(MatchName::class, 'EzSystems\EzPlatformSiteFactory\Values\Query\Criterion\MatchName');
