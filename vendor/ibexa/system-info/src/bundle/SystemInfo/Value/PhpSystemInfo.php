<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\SystemInfo\SystemInfo\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

/**
 * Value for information about the PHP interpreter and accelerator (if any) we are using.
 */
class PhpSystemInfo extends ValueObject implements SystemInfo
{
    /**
     * PHP version.
     *
     * Example: 5.6.7-1
     *
     * @var string
     */
    public $version;

    /**
     * True if an accelerator is enabled.
     *
     * @var bool
     */
    public $acceleratorEnabled;

    /**
     * PHP accelerator name, or null if no accelerator is enabled.
     *
     * Example: Zend OPcache
     *
     * @var string|null
     */
    public $acceleratorName;

    /**
     * PHP accelerator URL, or null if no accelerator is enabled.
     *
     * Example: http://www.php.net/opcache
     *
     * @var string|null
     */
    public $acceleratorURL;

    /**
     * PHP accelerator version, or null if no accelerator is enabled.
     *
     * Example: 7.0.4-devFE
     *
     * @var string|null
     */
    public $acceleratorVersion;
}

class_alias(PhpSystemInfo::class, 'EzSystems\EzSupportToolsBundle\SystemInfo\Value\PhpSystemInfo');
