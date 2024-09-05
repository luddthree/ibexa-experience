<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Mapper\Search;

use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Value\Search\SearchHit;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class SearchAttributesResultMapper implements SearchAttributesResultMapperInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    public function __construct(
        OutputTypeAttributesResolverInterface $outputTypeAttributesResolver,
        ?LoggerInterface $logger = null
    ) {
        $this->outputTypeAttributesResolver = $outputTypeAttributesResolver;
        $this->logger = $logger ?? new NullLogger();
    }

    public function map(int $customerId, array $searchResult): array
    {
        $outputTypeAttributes = $this->outputTypeAttributesResolver->resolve($customerId);

        $resultList = [];
        foreach ($searchResult as $searchHit) {
            if (!isset($outputTypeAttributes[$searchHit['itemType']][Personalization::TITLE_ATTR_NAME])) {
                $this->logger->info(sprintf(
                    'Output type attributes not configured for item type id: %s and parameter %s',
                    $searchHit['itemType'],
                    Personalization::TITLE_ATTR_NAME
                ));

                continue;
            }

            $itemType = (int) $searchHit['itemType'];
            $itemTypeName = $outputTypeAttributes[$itemType][Personalization::TITLE_ATTR_NAME];
            foreach ($searchHit['attributes'] as $attribute) {
                if ($attribute['name'] === $itemTypeName) {
                    $resultList[] = new SearchHit(
                        $searchHit['externalId'],
                        $itemType,
                        $attribute['value']
                    );
                }
            }
        }

        return $resultList;
    }
}
