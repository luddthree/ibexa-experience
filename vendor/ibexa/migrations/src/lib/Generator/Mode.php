<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

use Ibexa\Migration\Generator\Exception\InvalidModeException;

class Mode
{
    public const CREATE = 'create';
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const CALL = 'call';
    public const EXECUTE = 'execute';

    /**
     * @var array<string, string>
     */
    private const MODES = [
        self::CREATE => self::CREATE,
        self::UPDATE => self::UPDATE,
        self::DELETE => self::DELETE,
        self::CALL => self::CALL,
    ];

    /** @var string */
    private $mode;

    public function __construct(string $mode)
    {
        $this->guardMode($mode);
        $this->mode = $mode;
    }

    private function guardMode(string $mode): void
    {
        if (false === array_key_exists($mode, self::MODES)) {
            throw new InvalidModeException($mode, self::MODES);
        }
    }

    public function getMode(): string
    {
        return $this->mode;
    }
}

class_alias(Mode::class, 'Ibexa\Platform\Migration\Generator\Mode');
