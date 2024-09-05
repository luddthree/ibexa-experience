<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Exception\Registry;

use Exception;
use Throwable;

class AttributeFormMapperNotFoundException extends Exception
{
    /** @var string */
    private $type;

    /**
     * @param string $type
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($type, int $code = 0, Throwable $previous = null)
    {
        $this->type = $type;

        parent::__construct(
            sprintf(
                'Could not find AttributeFormMapper for type "%s". Did you register and tag ("%s") the service?',
                $type,
                'ibexa.page_builder.form_type_attribute.mapper'
            ),
            $code,
            $previous
        );
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}

class_alias(AttributeFormMapperNotFoundException::class, 'EzSystems\EzPlatformPageFieldType\Exception\Registry\AttributeFormMapperNotFoundException');
