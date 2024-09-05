<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\Generator\Role\StepBuilder;

use function array_map;
use Ibexa\Contracts\Core\Repository\Values\User\Policy as APIPolicy;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\Generator\Reference\RoleGenerator as ReferenceRoleGenerator;
use Ibexa\Migration\Generator\StepBuilder\StepBuilderInterface;
use Ibexa\Migration\ValueObject\Step\Role\Matcher;
use Ibexa\Migration\ValueObject\Step\Role\Policy;
use Ibexa\Migration\ValueObject\Step\Role\PolicyList;
use Ibexa\Migration\ValueObject\Step\Role\UpdateMetadata;
use Ibexa\Migration\ValueObject\Step\RoleUpdateStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Webmozart\Assert\Assert;

final class RoleUpdateStepBuilder implements StepBuilderInterface
{
    /** @var \Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager */
    private $limitationConverter;

    /** @var \Ibexa\Migration\Generator\Reference\RoleGenerator */
    private $referenceRoleGenerator;

    public function __construct(ReferenceRoleGenerator $referenceRoleGenerator, LimitationConverterManager $limitationConverter)
    {
        $this->limitationConverter = $limitationConverter;
        $this->referenceRoleGenerator = $referenceRoleGenerator;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $valueObject
     */
    public function build(ValueObject $valueObject): StepInterface
    {
        Assert::isInstanceOf($valueObject, Role::class);

        $convertPolicy = function (APIPolicy $policy): Policy {
            $limitations = array_map(
                [$this->limitationConverter, 'convertApiToMigration'],
                $policy->limitations
            );

            return new Policy(
                $policy->module,
                $policy->function,
                $limitations
            );
        };

        $policies = array_map(
            $convertPolicy,
            $valueObject->policies
        );

        $policyList = new PolicyList($policies);

        $references = $this->referenceRoleGenerator->generate($valueObject);

        return new RoleUpdateStep(
            new Matcher(Matcher::IDENTIFIER, $valueObject->identifier),
            new UpdateMetadata($valueObject->identifier),
            $policyList,
            $references,
        );
    }
}

class_alias(RoleUpdateStepBuilder::class, 'Ibexa\Platform\Migration\Generator\Role\StepBuilder\RoleUpdateStepBuilder');
