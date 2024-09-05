<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Mapper;

use Ibexa\Personalization\Form\Data\SubmodelData;
use Symfony\Component\Form\FormBuilderInterface;

interface SubmodelMapperInterface
{
    public function mapSubmodelForm(
        FormBuilderInterface $builder,
        SubmodelData $submodel
    ): void;
}

class_alias(SubmodelMapperInterface::class, 'Ibexa\Platform\Personalization\Form\Mapper\SubmodelMapperInterface');
