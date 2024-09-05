<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Reference;

use League\Flysystem\FilesystemOperator;
use Symfony\Component\Yaml\Yaml;

final class Dumper implements DumperInterface
{
    /** @var CollectorInterface */
    private $collector;

    private FilesystemOperator $filesystem;

    public function __construct(
        CollectorInterface $collector,
        FilesystemOperator $filesystem
    ) {
        $this->collector = $collector;
        $this->filesystem = $filesystem;
    }

    public function dump(string $filename): void
    {
        $references = '';

        foreach ($this->collector->getCollection()->getAll() as $reference) {
            $referenceString = Yaml::dump([[
                'name' => $reference->getName(),
                'value' => $reference->getValue(),
            ]]);

            $references .= PHP_EOL . $referenceString;
        }

        $this->filesystem->write($filename, $references);
    }
}

class_alias(Dumper::class, 'Ibexa\Platform\Migration\Reference\Dumper');
