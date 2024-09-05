<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Taxonomy\PHPUnit;

use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Test\Core\IbexaTestKernel;
use Ibexa\Core\Persistence\Legacy\Content\Gateway as ContentGateway;
use Ibexa\Core\Search\Common\Indexer;
use PHPUnit\Runner\AfterLastTestHook;
use PHPUnit\Runner\AfterTestHook;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class PHPUnitSearchExtension implements AfterTestHook, AfterLastTestHook
{
    private const MODIFIES_SEARCH_INDEX_ANNOTATION = '@modifiesSearchIndex';

    private static IbexaTestKernel $testKernel;

    public static function setTestKernel(IbexaTestKernel $testKernel): void
    {
        self::$testKernel = $testKernel;
    }

    public function executeAfterTest(string $test, float $time): void
    {
        if (getenv('SEARCH_ENGINE') === 'legacy') {
            return;
        }

        if ($this->isAnnotatedWith($test, self::MODIFIES_SEARCH_INDEX_ANNOTATION)) {
            $this->indexAll(self::$testKernel->getContainer());
        }
    }

    private function isAnnotatedWith(string $test, string $annotation): bool
    {
        try {
            $comment = (new ReflectionMethod($test))->getDocComment();
            if ($comment !== false) {
                return str_contains($comment, $annotation);
            }
        } catch (ReflectionException $e) {
        }

        return false;
    }

    private function indexAll(ContainerInterface $container): void
    {
        /** @var \Ibexa\Core\Search\Common\IncrementalIndexer $indexer */
        $indexer = $container->get('test.' . Indexer::class);
        $indexer->purge();
        $indexer->updateSearchIndex(
            $this->getContentIdsToIndex($container),
            true
        );
    }

    /**
     * @return int[]
     */
    private function getContentIdsToIndex(ContainerInterface $container): array
    {
        /** @var \Doctrine\DBAL\Connection $connection */
        $connection = $container->get('ibexa.persistence.connection');

        $query = $connection->createQueryBuilder();
        $query
            ->select('id')
            ->from(ContentGateway::CONTENT_ITEM_TABLE)
            ->where($query->expr()->eq('status', ContentInfo::STATUS_PUBLISHED));

        return array_map(
            'intval',
            $query->execute()->fetchFirstColumn()
        );
    }

    public function executeAfterLastTest(): void
    {
        if (isset(self::$testKernel)) {
            self::$testKernel->shutdown();
        }
    }
}
