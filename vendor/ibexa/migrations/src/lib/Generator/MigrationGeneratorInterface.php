<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator;

interface MigrationGeneratorInterface
{
    public function supports(string $migrationType, Mode $migrationMode): bool;

    /**
     * @return string Migration type that generator can handle
     */
    public function getSupportedType(): string;

    /**
     * @return string[] Migration modes that generator can handle
     */
    public function getSupportedModes(): array;

    /**
     * @param \Ibexa\Migration\Generator\Mode $migrationMode
     * @param array{
     *     value: scalar[],
     *     match-property?: ?string,
     * } $context
     *
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable;
}

class_alias(MigrationGeneratorInterface::class, 'Ibexa\Platform\Migration\Generator\MigrationGeneratorInterface');
