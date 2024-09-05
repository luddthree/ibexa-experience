<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImagePicker\Behat\Component\Fields;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Fields\FieldTypeComponent;
use Ibexa\AdminUi\Behat\Component\Fields\ImageAsset as BaseImageAsset;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\ImagePicker\Behat\Component\ImagePicker;

final class ImageAsset extends FieldTypeComponent
{
    private BaseImageAsset $imageAssetField;

    private ImagePicker $imagePicker;

    public function __construct(Session $session, BaseImageAsset $imageAssetField, ImagePicker $imagePicker)
    {
        $this->imageAssetField = $imageAssetField;
        $this->imagePicker = $imagePicker;
        parent::__construct($session);
    }

    /** @param array<mixed> $parameters */
    public function setValue(array $parameters): void
    {
        $this->imageAssetField->setValue($parameters);
    }

    public function selectFromRepository(string $path): void
    {
        $this->getHTMLPage()
            ->find($this->parentLocator)
            ->find($this->getLocator('selectFromRepoButton'))
            ->click();

        $pathParts = explode('/', $path);
        $imageName = array_pop($pathParts);

        $this->imagePicker->verifyIsLoaded();
        $this->imagePicker->search($imageName);
        $this->imagePicker->selectImage($imageName);
        $this->imagePicker->confirm();
    }

    /** @return array<mixed> */
    public function getValue(): array
    {
        return $this->imageAssetField->getValue();
    }

    /** @param array<mixed> $values */
    public function verifyValueInItemView(array $values): void
    {
        $this->imageAssetField->verifyValueInItemView($values);
    }

    /** @param array<mixed> $values */
    public function verifyValueInEditView(array $values): void
    {
        $this->imageAssetField->verifyValueInEditView($values);
    }

    public function getFieldTypeIdentifier(): string
    {
        return $this->imageAssetField->getFieldTypeIdentifier();
    }

    public function setParentLocator(VisibleCSSLocator $selector): void
    {
        parent::setParentLocator($selector);
        $this->imageAssetField->setParentLocator($selector);
    }

    public function specifyLocators(): array
    {
        return $this->imageAssetField->specifyLocators();
    }
}
