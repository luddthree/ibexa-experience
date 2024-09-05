<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Serializer\Denormalizer\Content\Action;

use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\MatchingAssertionTrait;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\Action\UnpackActionValueTrait;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\AbstractActionDenormalizer;
use Ibexa\Migration\ValueObject\Step\Action\Content\AssignSection;
use Webmozart\Assert\Assert;

final class AssignSectionActionDenormalizer extends AbstractActionDenormalizer
{
    use UnpackActionValueTrait;

    use MatchingAssertionTrait;

    protected function supportsActionName(string $actionName, string $format = null): bool
    {
        return $actionName === AssignSection::TYPE;
    }

    /**
     * @param array<mixed> $data
     * @param array<mixed> $context
     */
    public function denormalize($data, string $type, string $format = null, array $context = []): AssignSection
    {
        Assert::isArray($data);
        $data = $this->unpackValue($data);

        $matchingMethods = [];
        $id = null;
        if (isset($data['id'])) {
            $matchingMethods[] = 'id';
            $id = (int) $data['id'];
        }

        $identifier = null;
        if (isset($data['identifier'])) {
            $matchingMethods[] = 'identifier';
            $identifier = (string) $data['identifier'];
        }

        $this->throwIfNoMatchingMethod(AssignSection::TYPE, $matchingMethods, ['id', 'identifier']);
        $this->throwIfMoreThanOneMatchingMethod(AssignSection::TYPE, $matchingMethods);

        return new AssignSection($id, $identifier);
    }
}
