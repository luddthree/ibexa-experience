<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Security\Limitation\Mapper;

use Ibexa\AdminUi\Limitation\LimitationValueMapperInterface;
use Ibexa\AdminUi\Limitation\Mapper\MultipleSelectionBasedMapper;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\Values\User\Limitation;
use Ibexa\Contracts\ProductCatalog\Exceptions\ConfigurationException;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ProductTypeLimitationMapper extends MultipleSelectionBasedMapper implements LimitationValueMapperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ProductTypeServiceInterface $productTypeService;

    public function __construct(
        ProductTypeServiceInterface $productTypeService,
        ?LoggerInterface $logger = null
    ) {
        $this->productTypeService = $productTypeService;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @return array<string, string>
     */
    protected function getSelectionChoices(): array
    {
        $productTypeChoices = [];
        $productTypes = [];
        try {
            $productTypes = $this->productTypeService->findProductTypes()->getProductTypes();
        } catch (UnauthorizedException | ConfigurationException $e) {
            $this->logger->error("Can't fetch list of Product Types. " . $e->getMessage());
        }

        foreach ($productTypes as $productType) {
            $productTypeChoices[$productType->getIdentifier()] = $productType->getName();
        }

        return $productTypeChoices;
    }

    public function mapLimitationValue(Limitation $limitation)
    {
        $values = [];
        foreach ($limitation->limitationValues as $productTypeIdentifier) {
            try {
                $values[] = $this->productTypeService->getProductType($productTypeIdentifier);
            } catch (NotFoundException $e) {
                $this->logger->error(sprintf('Could not map the Limitation value: could not find a Product Type with identifier %s', $productTypeIdentifier));
            }
        }

        return $values;
    }
}
