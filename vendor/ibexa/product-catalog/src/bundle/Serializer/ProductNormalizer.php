<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Serializer;

use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\User\UserSetting\DateTimeFormat\FormatterInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductNormalizer implements NormalizerInterface
{
    private TranslatorInterface $translator;

    private UrlGeneratorInterface $urlGenerator;

    private FormatterInterface $fullDateTimeFormatter;

    public function __construct(
        TranslatorInterface $translator,
        UrlGeneratorInterface $urlGenerator,
        FormatterInterface $fullDateTimeFormatter
    ) {
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->fullDateTimeFormatter = $fullDateTimeFormatter;
    }

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Values\Product $object
     *
     * @phpstan-return array{
     *  name: string,
     *  thumbnail: string,
     *  code: string,
     *  type: string,
     *  created_at: string,
     *  is_available: bool,
     *  stock: string,
     *  view_url: string,
     *  edit_url: string,
     * }
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'name' => $object->getName(),
            'thumbnail' => $object->getThumbnail()->resource ?? '',
            'code' => $object->getCode(),
            'type' => $object->getProductType()->getName(),
            'created_at' => $this->fullDateTimeFormatter->format($object->getCreatedAt()),
            'is_available' => $this->isAvailable($object),
            'stock' => $this->getStock($object),
            'view_url' => $this->urlGenerator->generate(
                'ibexa.product_catalog.product.view',
                ['productCode' => $object->getCode()]
            ),
            'edit_url' => $this->urlGenerator->generate(
                'ibexa.product_catalog.product.edit',
                ['productCode' => $object->getCode()]
            ),
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof ProductInterface;
    }

    private function isAvailable(ProductInterface $product): bool
    {
        return $product instanceof AvailabilityAwareInterface
            && $product->hasAvailability()
            && $product->getAvailability()->isAvailable();
    }

    private function getStock(ProductInterface $product): string
    {
        if (!$product instanceof AvailabilityAwareInterface || !$product->hasAvailability()) {
            return '';
        }

        if ($product->getAvailability()->isInfinite()) {
            return $this->translator->trans(
                /** @Desc("Unlimited") */
                'product.list.unlimited',
                [],
                'ibexa_product_catalog'
            );
        }

        return (string)$product->getAvailability()->getStock();
    }
}
