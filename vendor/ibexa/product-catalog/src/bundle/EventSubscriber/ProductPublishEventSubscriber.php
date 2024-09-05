<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\GatewayInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Webmozart\Assert\Assert;

final class ProductPublishEventSubscriber implements EventSubscriberInterface
{
    private GatewayInterface $productGateway;

    public function __construct(GatewayInterface $productGateway)
    {
        $this->productGateway = $productGateway;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => [
                'onProductPublish',
            ],
        ];
    }

    public function onProductPublish(PublishVersionEvent $event): void
    {
        $content = $event->getContent();
        foreach ($content->getFields() as $field) {
            if ($field->getFieldTypeIdentifier() !== ProductSpecificationType::FIELD_TYPE_IDENTIFIER) {
                continue;
            }

            $value = $field->getValue();
            Assert::isInstanceOf($value, Value::class);

            $originalCode = $value->getOriginalCode();
            Assert::string($originalCode, sprintf(
                '%s object is incomplete. Original code is null.',
                Value::class,
            ));

            $this->productGateway->update(
                $originalCode,
                $value->getCode(),
                true,
            );
        }
    }
}
