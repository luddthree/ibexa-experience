<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Seo\Form\Type\Extension;

use Ibexa\AdminUi\Form\Data\ContentTypeData;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\AdminUi\Form\Type\ContentType\FieldDefinitionsCollectionType;
use Ibexa\AdminUi\Form\Type\Extension\ModifyFieldDefinitionsCollectionTypeExtension;
use Ibexa\Contracts\Core\Test\IbexaKernelTestCase;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Core\Repository\Values\ContentType\ContentTypeDraft;
use Ibexa\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Seo\Value\SeoTypesValue;
use Symfony\Component\Form\FormFactoryInterface;

final class DisableRequiredOptionForIbexaSeoTest extends IbexaKernelTestCase
{
    private const OPTION_DISABLE_REQUIRED_FIELD = 'disable_required_field';

    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        $this->formFactory = self::getServiceByClassName(FormFactoryInterface::class);
    }

    public function testFormBuild(): void
    {
        $data = $this->createFormData();
        $form = $this->formFactory->create(FieldDefinitionsCollectionType::class, $data, [
            'languageCode' => 'eng-GB',
        ]);

        $config = $form->getConfig();
        self::assertContainsModifyFieldDefinitionsExtension($config->getType()->getTypeExtensions());

        $metadata = $form->get('metadata');

        $seoField = $metadata->get('ibexa_seo');
        $seoFieldConfig = $seoField->getConfig();
        self::assertTrue($seoFieldConfig->hasOption(self::OPTION_DISABLE_REQUIRED_FIELD));
        self::assertTrue($seoFieldConfig->getOption(self::OPTION_DISABLE_REQUIRED_FIELD));
    }

    /**
     * @param array<\Symfony\Component\Form\FormTypeExtensionInterface> $extensions
     */
    private static function assertContainsModifyFieldDefinitionsExtension(array $extensions): void
    {
        foreach ($extensions as $extension) {
            if ($extension instanceof ModifyFieldDefinitionsCollectionTypeExtension) {
                return;
            }
        }

        self::fail(sprintf('Missing %s extension', ModifyFieldDefinitionsCollectionTypeExtension::class));
    }

    /**
     * @return array<string, mixed>
     */
    private function createFormData(): array
    {
        return [
            'metadata' => [
                'ibexa_seo' => new FieldDefinitionData(
                    [
                        'fieldSettings' => [
                            'types' => new SeoTypesValue(),
                        ],
                        'contentTypeData' => new ContentTypeData([
                            'contentTypeDraft' => new ContentTypeDraft([
                                'innerContentType' => new ContentType([
                                    'identifier' => 'test_content_type',
                                ]),
                            ]),
                            'languageCode' => 'eng-GB',
                        ]),
                        'identifier' => 'ibexa_seo',
                        'isRequired' => true,
                        'isThumbnail' => false,
                        'isInfoCollector' => false,
                        'fieldDefinition' => new FieldDefinition(
                            [
                                'identifier' => 'foo',
                                'fieldTypeIdentifier' => 'ibexa_seo',
                            ]
                        ),
                    ]
                ),
            ],
        ];
    }
}
