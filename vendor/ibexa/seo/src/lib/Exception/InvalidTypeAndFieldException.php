<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Exception;

use Exception;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException as APIInvalidArgumentException;
use Ibexa\Core\Base\Translatable;
use Ibexa\Core\Base\TranslatableBase;

final class InvalidTypeAndFieldException extends APIInvalidArgumentException implements Translatable
{
    use TranslatableBase;

    /**
     * Generates: "Unable to render SEO tag for type '{$typeName}' and field '{$fieldName}'".
     *
     * @param \Exception|null $previous
     */
    public function __construct(string $typeName, string $fieldName, Exception $previous = null)
    {
        $this->setMessageTemplate("Unable to render SEO tag for type '%typeName%' and field '%fieldName%'");
        $this->setParameters(['%typeName%' => $typeName, '%fieldName%' => $fieldName]);
        parent::__construct($this->getBaseTranslation(), 0, $previous);
    }
}
