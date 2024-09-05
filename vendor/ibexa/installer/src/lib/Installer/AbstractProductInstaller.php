<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Installer\Installer;

use Doctrine\DBAL\Connection;
use Ibexa\Bundle\RepositoryInstaller\Installer\CoreInstaller;
use Ibexa\Contracts\DoctrineSchema\Builder\SchemaBuilderInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractProductInstaller extends CoreInstaller
{
    /** @var iterable<\Ibexa\Installer\Provisioner\ProvisionerInterface> */
    private $provisioners;

    /**
     * @param iterable<\Ibexa\Installer\Provisioner\ProvisionerInterface> $provisioners
     */
    public function __construct(
        Connection $db,
        SchemaBuilderInterface $schemaBuilder,
        iterable $provisioners
    ) {
        parent::__construct($db, $schemaBuilder);

        $this->provisioners = $provisioners;
    }

    public function importData(): void
    {
        parent::importData(); // install clean data from Kernel

        foreach ($this->provisioners as $provisioner) {
            $this->output->writeln(
                sprintf('Executing provisioner: %s', get_class($provisioner)),
                OutputInterface::VERBOSITY_DEBUG
            );

            $provisioner->provision($this->output);
        }
    }
}
