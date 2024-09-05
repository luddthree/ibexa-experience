<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\GraphQL\Resolver;

use GraphQL\Type\Definition\Type;
use Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface;
use Overblog\GraphQLBundle\Resolver\TypeResolver;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class SchedulerBlockResolver
{
    /** @var \Overblog\GraphQLBundle\Resolver\TypeResolver */
    private $typeResolver;

    /** @var \Symfony\Component\Serializer\NameConverter\NameConverterInterface */
    private $nameConverter;

    /**
     * @param \Overblog\GraphQLBundle\Resolver\TypeResolver $typeResolver
     * @param \Symfony\Component\Serializer\NameConverter\NameConverterInterface $nameConverter
     */
    public function __construct(TypeResolver $typeResolver, NameConverterInterface $nameConverter = null)
    {
        $this->typeResolver = $typeResolver;
        $this->nameConverter = $nameConverter ?? new CamelCaseToSnakeCaseNameConverter(null, false);
    }

    /**
     * @param \Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event\EventInterface $event
     *
     * @return \GraphQL\Type\Definition\Type
     */
    public function resolverSchedulerBlockEventType(EventInterface $event): Type
    {
        return $this->typeResolver->resolve($this->eventTypeName($event->getType()));
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function eventTypeName(string $type): string
    {
        return $this->nameConverter->denormalize($type) . 'SchedulerBlockEvent';
    }
}

class_alias(SchedulerBlockResolver::class, 'EzSystems\EzPlatformPageFieldType\GraphQL\Resolver\SchedulerBlockResolver');
