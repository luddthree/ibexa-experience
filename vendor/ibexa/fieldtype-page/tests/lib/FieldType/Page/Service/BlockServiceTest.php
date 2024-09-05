<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\FieldType\Page\Service;

use Ibexa\Contracts\Core\FieldType\ValidationError;
use Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactoryInterface;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\RendererInterface;
use Ibexa\FieldTypePage\FieldType\Page\Service\BlockService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @covers \Ibexa\FieldTypePage\FieldType\Page\Service\BlockService
 */
final class BlockServiceTest extends TestCase
{
    private BlockService $blockService;

    /**
     * @return iterable<
     *      string,
     *      array{
     *          \Ibexa\Contracts\FieldTypePage\FieldType\LandingPage\Model\BlockValue,
     *          \Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition,
     *          string[]
     *      }
     * >
     */
    public static function getDataForTestValidateBlock(): iterable
    {
        $blockDefinition = new BlockDefinition();
        $blockDefinitionName = 'Foo Block';
        $blockDefinition->setName($blockDefinitionName);
        $blockValueName = str_repeat('x', 256);
        yield 'block name too long' => [
            new BlockValue('1', 'foo', $blockValueName, '', null, null, null, null, null, []),
            $blockDefinition,
            [
                sprintf(
                    "Value '%s' in block '%s' is invalid: This value is too long. It should have 255 characters or less.",
                    $blockValueName,
                    $blockDefinitionName
                ),
            ],
        ];

        yield 'max length block name' => [
            new BlockValue('1', 'foo', str_repeat('x', 255), '', null, null, null, null, null, []),
            $blockDefinition,
            [
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->blockService = new BlockService(
            $this->createMock(EventDispatcherInterface::class),
            $this->createMock(RendererInterface::class),
            $this->createMock(BlockDefinitionFactoryInterface::class),
            new ConstraintValidatorFactory(),
            $this->createMock(ConstraintFactory::class)
        );
    }

    /**
     * @dataProvider getDataForTestValidateBlock
     *
     * @param string[] $expectedValidationErrors
     *
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeValidatorNotFoundException
     */
    public function testValidateBlock(
        BlockValue $blockValue,
        BlockDefinition $blockDefinition,
        array $expectedValidationErrors
    ): void {
        self::assertSame(
            $expectedValidationErrors,
            array_map(
                static function (ValidationError $validationError): string {
                    return (string)$validationError->getTranslatableMessage();
                },
                $this->blockService->validateBlock($blockValue, $blockDefinition)
            )
        );
    }
}
