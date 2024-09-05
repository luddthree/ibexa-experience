<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use Ibexa\FieldTypePage\Exception\PageNotFoundException;
use ScssPhp\ScssPhp\Compiler;

class DoctrineGateway extends Gateway
{
    public const TABLE_PAGES = 'ezpage_pages';
    public const TABLE_ZONES = 'ezpage_zones';
    public const TABLE_BLOCKS = 'ezpage_blocks';
    public const TABLE_BLOCKS_DESIGN = 'ezpage_blocks_design';
    public const TABLE_BLOCKS_VISIBILITY = 'ezpage_blocks_visibility';
    public const TABLE_ATTRIBUTES = 'ezpage_attributes';
    public const TABLE_MAP_ZONES_PAGES = 'ezpage_map_zones_pages';
    public const TABLE_MAP_BLOCKS_ZONES = 'ezpage_map_blocks_zones';
    public const TABLE_MAP_ATTRIBUTES_BLOCKS = 'ezpage_map_attributes_blocks';

    /** @var \Doctrine\DBAL\Connection */
    protected $connection;

    /**
     * @param \Doctrine\DBAL\Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function insertPage(int $contentId, int $versionNo, string $languageCode, string $layout): ?int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_PAGES))
            ->values([
                'layout' => ':layout',
                'content_id' => ':content_id',
                'version_no' => ':version_no',
                'language_code' => ':language_code',
            ])
            ->setParameters([
                ':content_id' => $contentId,
                ':version_no' => $versionNo,
                ':language_code' => $languageCode,
                ':layout' => $layout,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_PAGES, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function removePage(int $id): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_PAGES))
            ->where('id = :page_id')
            ->setParameter(':page_id', $id, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function insertZone(string $name): ?int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_ZONES))
            ->values([
                'name' => ':name',
            ])
            ->setParameter(':name', $name, ParameterType::STRING);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_ZONES, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insertBlock(string $type, string $name, string $view): ?int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_BLOCKS))
            ->values([
                'type' => ':type',
                'name' => ':name',
                'view' => ':view',
            ])
            ->setParameters([
                ':type' => $type,
                ':name' => $name,
                ':view' => $view,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_BLOCKS, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insertBlockDesign(int $blockId, ?string $style, ?string $compiled, ?string $class): ?int
    {
        $query = $this->connection->createQueryBuilder();

        // @todo: Move to ScssType with data transformer
        $compiled = (new Compiler())->compile(sprintf(
            '[data-ez-block-id="%d"] { %s }',
            $blockId,
            $style
        ));

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_BLOCKS_DESIGN))
            ->values([
                'block_id' => ':block_id',
                'style' => ':style',
                'compiled' => ':compiled',
                'class' => ':class',
            ])
            ->setParameters([
                ':block_id' => $blockId,
                ':style' => $style,
                ':compiled' => $compiled,
                ':class' => $class,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_BLOCKS_DESIGN, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insertBlockVisibility(int $blockId, ?int $since, ?int $till): ?int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_BLOCKS_VISIBILITY))
            ->values([
                'block_id' => ':block_id',
                'since' => ':since',
                'till' => ':till',
            ])
            ->setParameters([
                ':block_id' => $blockId,
                ':since' => $since,
                ':till' => $till,
            ]);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_BLOCKS_VISIBILITY, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function insertAttribute(string $name, ?string $value): ?int
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_ATTRIBUTES))
            ->values([
                'name' => ':name',
                'value' => ':value',
            ])
            ->setParameter(':name', $name, ParameterType::STRING)
            ->setParameter(':value', $value, null !== $value ? ParameterType::STRING : ParameterType::NULL);

        $query->execute();

        return (int)$this->connection->lastInsertId(
            $this->getSequenceName(self::TABLE_ATTRIBUTES, 'id')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function assignAttributeToBlock(int $attributeId, int $blockId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_MAP_ATTRIBUTES_BLOCKS))
            ->values([
                'attribute_id' => ':attribute_id',
                'block_id' => ':block_id',
            ])
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER)
            ->setParameter(':attribute_id', $attributeId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function assignBlockToZone(int $blockId, int $zoneId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_MAP_BLOCKS_ZONES))
            ->values([
                'block_id' => ':block_id',
                'zone_id' => ':zone_id',
            ])
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER)
            ->setParameter(':zone_id', $zoneId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function assignZoneToPage(int $zoneId, int $pageId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->insert($this->connection->quoteIdentifier(self::TABLE_MAP_ZONES_PAGES))
            ->values([
                'zone_id' => ':zone_id',
                'page_id' => ':page_id',
            ])
            ->setParameter(':zone_id', $zoneId, ParameterType::INTEGER)
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function loadPage(int $pageId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('layout')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_PAGES))
            ->where(
                $selectQuery->expr()->eq(
                    $this->connection->quoteIdentifier('id'),
                    ':page_id'
                )
            )
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new PageNotFoundException("pageId: $pageId");
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function loadPageByContentId(int $contentId, int $versionNo, string $languageCode): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('layout'),
            $this->connection->quoteIdentifier('content_id'),
            $this->connection->quoteIdentifier('version_no'),
            $this->connection->quoteIdentifier('language_code')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_PAGES))
            ->where(
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('content_id'), ':content_id'),
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('version_no'), ':version_no'),
                $selectQuery->expr()->eq($this->connection->quoteIdentifier('language_code'), ':language_code')
            )
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER)
            ->setParameter(':version_no', $versionNo, ParameterType::INTEGER)
            ->setParameter(':language_code', $languageCode, ParameterType::STRING);

        $statement = $selectQuery->execute();

        $data = $statement->fetch(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new PageNotFoundException(
                "contentId: $contentId, versionNo: $versionNo, languageCode: $languageCode"
            );
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function loadPagesMappedToContent(int $contentId, ?int $versionNo = null, array $languageCodes = []): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery->select(
            $this->connection->quoteIdentifier('id'),
            $this->connection->quoteIdentifier('layout'),
            $this->connection->quoteIdentifier('content_id'),
            $this->connection->quoteIdentifier('version_no'),
            $this->connection->quoteIdentifier('language_code')
        );

        $selectQuery
            ->from($this->connection->quoteIdentifier(self::TABLE_PAGES))
            ->where('content_id = :content_id')
            ->setParameter(':content_id', $contentId, ParameterType::INTEGER);

        if (null !== $versionNo) {
            $selectQuery
                ->andWhere('version_no = :version_no')
                ->setParameter(':version_no', $versionNo, ParameterType::INTEGER);
        }

        if (!empty($languageCodes)) {
            $selectQuery
                ->andWhere('language_code IN (:language_codes)')
                ->setParameter(':language_codes', $languageCodes, Connection::PARAM_STR_ARRAY);
        }

        $statement = $selectQuery->execute();

        $data = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        if (!$data) {
            throw new PageNotFoundException($this->buildIdentifierString($contentId, $versionNo, $languageCodes));
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function removeZone(int $zoneId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_ZONES))
            ->where('id = :zone_id')
            ->setParameter(':zone_id', $zoneId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function removeBlock(int $blockId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_BLOCKS))
            ->where('id = :block_id')
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function removeAttribute(int $attributeId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_ATTRIBUTES))
            ->where('id = :attribute_id')
            ->setParameter(':attribute_id', $attributeId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function loadZonesAssignedToPage(int $pageId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select(
                $this->connection->quoteIdentifier('z.id'),
                $this->connection->quoteIdentifier('z.name')
            )
            ->from($this->connection->quoteIdentifier(self::TABLE_ZONES), 'z')
            ->leftJoin(
                'z',
                $this->connection->quoteIdentifier(self::TABLE_MAP_ZONES_PAGES),
                'mzp',
                'mzp.zone_id = z.id'
            )
            ->where('mzp.page_id = :page_id')
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function loadBlocksAssignedToPage(int $pageId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();

        $selectQuery
            ->select([
                'b.id',
                'b.type',
                'b.view',
                'b.name',
                'bd.class',
                'bd.style',
                'bd.compiled',
                'bv.since',
                'bv.till',
                'mzp.zone_id',
            ])
            ->from($this->connection->quoteIdentifier(self::TABLE_BLOCKS), 'b')
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(self::TABLE_MAP_BLOCKS_ZONES),
                'mbz',
                'mbz.block_id = b.id'
            )
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(self::TABLE_MAP_ZONES_PAGES),
                'mzp',
                'mzp.zone_id = mbz.zone_id'
            )
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(self::TABLE_BLOCKS_DESIGN),
                'bd',
                'bd.block_id = b.id'
            )
            ->leftJoin(
                'b',
                $this->connection->quoteIdentifier(self::TABLE_BLOCKS_VISIBILITY),
                'bv',
                'bv.block_id = b.id'
            )
            ->where('mzp.page_id = :page_id')
            ->orderBy('b.id', 'ASC')
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function loadAttributesAssignedToPage(int $pageId): array
    {
        $selectQuery = $this->connection->createQueryBuilder();
        $selectQuery
            ->select([
                'a.id',
                'a.name',
                'a.value',
                'mab.block_id',
                'mbz.zone_id',
            ])
            ->from($this->connection->quoteIdentifier(self::TABLE_ATTRIBUTES), 'a')
            ->leftJoin(
                'a',
                $this->connection->quoteIdentifier(self::TABLE_MAP_ATTRIBUTES_BLOCKS),
                'mab',
                'mab.attribute_id = a.id'
            )
            ->leftJoin(
                'a',
                $this->connection->quoteIdentifier(self::TABLE_MAP_BLOCKS_ZONES),
                'mbz',
                'mab.block_id = mbz.block_id'
            )
            ->leftJoin(
                'a',
                $this->connection->quoteIdentifier(self::TABLE_MAP_ZONES_PAGES),
                'mzp',
                'mbz.zone_id = mzp.zone_id'
            )
            ->where('mzp.page_id = :page_id')
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $statement = $selectQuery->execute();

        return $statement->fetchAll(FetchMode::ASSOCIATIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function unassignAttributeFromBlock(int $attributeId, int $blockId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_MAP_ATTRIBUTES_BLOCKS))
            ->where('attribute_id = :attribute_id', 'block_id = :block_id')
            ->setParameter(':attribute_id', $attributeId, ParameterType::INTEGER)
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function unassignBlockFromZone(int $blockId, int $zoneId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_MAP_BLOCKS_ZONES))
            ->where('block_id = :block_id', 'zone_id = :zone_id')
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER)
            ->setParameter(':zone_id', $zoneId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function unassignZoneFromPage(int $zoneId, int $pageId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_MAP_ZONES_PAGES))
            ->where('zone_id = :zone_id', 'page_id = :page_id')
            ->setParameter(':zone_id', $zoneId, ParameterType::INTEGER)
            ->setParameter(':page_id', $pageId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $blockId
     */
    public function removeBlockDesign(int $blockId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_BLOCKS_DESIGN))
            ->where('block_id = :block_id')
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $blockId
     */
    public function removeBlockVisibility(int $blockId): void
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->delete($this->connection->quoteIdentifier(self::TABLE_BLOCKS_VISIBILITY))
            ->where('block_id = :block_id')
            ->setParameter(':block_id', $blockId, ParameterType::INTEGER);

        $query->execute();
    }

    /**
     * @param int $contentId
     * @param int|null $versionNo
     * @param string[] $languageCodes
     *
     * @return string
     */
    private function buildIdentifierString(int $contentId, ?int $versionNo = null, array $languageCodes = []): string
    {
        $identifier = "contentId: $contentId";

        if (null !== $versionNo) {
            $identifier .= ", versionNo: $versionNo";
        }

        if (!empty($languageCodes)) {
            $identifier .= sprintf(', languageCodes: [%s]', implode(', ', $languageCodes));
        }

        return $identifier;
    }
}

class_alias(DoctrineGateway::class, 'EzSystems\EzPlatformPageFieldType\FieldType\Page\Storage\DoctrineGateway');
