<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

use Ibexa\Migration\ValueObject\Reference\Reference;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToReadFile;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;
use Webmozart\Assert\Assert;

final class Loader implements LoaderInterface
{
    /** @var CollectorInterface */
    private $collector;

    private FilesystemOperator $filesystem;

    /** @var string */
    private $migrationPath;

    public function __construct(
        CollectorInterface $collector,
        FilesystemOperator $filesystem,
        string $migrationPath
    ) {
        $this->collector = $collector;
        $this->filesystem = $filesystem;
        $this->migrationPath = $migrationPath;
    }

    /**
     * @throws \League\Flysystem\FilesystemException
     */
    public function load(string $filename): void
    {
        $references = $this->readReferenceFile($filename);

        Assert::allKeyExists($references, 'name');
        Assert::allKeyExists($references, 'value');

        foreach ($references as $ref) {
            $this->collector->collect(Reference::create($ref['name'], $ref['value']));
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \League\Flysystem\FilesystemException
     */
    private function readReferenceFile(string $filename): array
    {
        try {
            return Yaml::parse($this->filesystem->read($filename));
        } catch (UnableToReadFile $e) {
            throw new RuntimeException(
                sprintf('Unable to read file %s at location %s', $filename, $this->migrationPath),
                0,
                $e
            );
        }
    }
}

class_alias(Loader::class, 'Ibexa\Platform\Migration\Reference\Loader');
