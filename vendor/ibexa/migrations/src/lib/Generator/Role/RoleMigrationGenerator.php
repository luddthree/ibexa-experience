<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Migration\Generator\MigrationGeneratorInterface;
use Ibexa\Migration\Generator\Mode;
use Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface;
use function in_array;
use Webmozart\Assert\Assert;

final class RoleMigrationGenerator implements MigrationGeneratorInterface
{
    private const TYPE = 'role';

    /** @var \Ibexa\Migration\Generator\StepBuilder\StepFactoryInterface */
    private $stepFactory;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(StepFactoryInterface $stepFactory, RoleService $roleService)
    {
        $this->stepFactory = $stepFactory;
        $this->roleService = $roleService;
    }

    public function supports(string $migrationType, Mode $migrationMode): bool
    {
        return $migrationType === $this->getSupportedType()
            && in_array($migrationMode->getMode(), $this->getSupportedModes(), true);
    }

    public function getSupportedType(): string
    {
        return self::TYPE;
    }

    public function getSupportedModes(): array
    {
        return $this->stepFactory->getSupportedModes();
    }

    /**
     * @return \Ibexa\Migration\ValueObject\Step\StepInterface[]
     */
    public function generate(Mode $migrationMode, array $context): iterable
    {
        foreach ($this->getRoles($context) as $role) {
            yield $this->stepFactory->create($role, $migrationMode);
        }
    }

    /**
     * @param array<mixed> $context
     *
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\Role>
     */
    private function getRoles(array $context): iterable
    {
        if (isset($context['match-property'])) {
            Assert::isIterable($context['value']);
            Assert::notEmpty($context['value']);
            $values = $context['value'];

            switch ($context['match-property']) {
                case 'identifier':
                    foreach ($values as $value) {
                        Assert::stringNotEmpty($value);
                        yield $this->roleService->loadRoleByIdentifier($value);
                    }

                    return;
                case 'id':
                    foreach ($values as $value) {
                        Assert::integerish($value);
                        yield $this->roleService->loadRole((int) $value);
                    }

                    return;
            }
        }

        yield from $this->roleService->loadRoles();
    }
}

class_alias(RoleMigrationGenerator::class, 'Ibexa\Platform\Migration\Generator\Role\RoleMigrationGenerator');
