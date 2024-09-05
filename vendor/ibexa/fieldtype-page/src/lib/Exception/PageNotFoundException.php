<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\FieldTypePage\Exception;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Core\Base\Exceptions\Httpable;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

/**
 * {@inheritdoc}
 */
class PageNotFoundException extends NotFoundException implements Translatable, Httpable
{
    use TranslatableBase;

    /**
     * @param string $identifier
     * @param \Exception|null $previous
     */
    public function __construct(string $identifier, Exception $previous = null)
    {
        $this->setMessageTemplate("Could not find Page for '%identifier%'");
        $this->setParameters(['%identifier%' => $identifier]);

        parent::__construct($this->getBaseTranslation(), self::NOT_FOUND, $previous);
    }
}

class_alias(PageNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\PageNotFoundException');
