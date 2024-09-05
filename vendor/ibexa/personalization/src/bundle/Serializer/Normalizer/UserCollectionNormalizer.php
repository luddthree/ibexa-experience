<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization\Serializer\Normalizer;

use Ibexa\Personalization\Value\Output\UserCollection;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class UserCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    public const ATTR_NAME = 'user';

    /** @var \Symfony\Component\Serializer\Normalizer\NormalizerInterface */
    private $owningNormalizer;

    /**
     * {@inheritdoc}()
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return $this->getNormalizedUsers($object);
    }

    /**
     * {@inheritdoc}()
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof UserCollection;
    }

    /**
     * {@inheritdoc}()
     */
    public function setNormalizer(NormalizerInterface $normalizer): void
    {
        $this->owningNormalizer = $normalizer;
    }

    /**
     * Normalizes UserCollection.
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    private function getNormalizedUsers(UserCollection $object): array
    {
        $users = (array)$this->owningNormalizer->normalize($object->getUsers());

        $normalizedUsers = [];
        $normalizedUsers[self::ATTR_NAME] = array_map(static function ($item) {
            return $item[self::ATTR_NAME];
        }, $users);

        return $normalizedUsers;
    }
}

class_alias(UserCollectionNormalizer::class, 'EzSystems\EzRecommendationClientBundle\Serializer\Normalizer\UserCollectionNormalizer');
