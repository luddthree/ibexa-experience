<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Section;
use Ibexa\Core\FieldType;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\Location as APILocation;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Migration\Generator\Reference\ReferenceDefinition;
use Ibexa\Migration\ValueObject\Content\CreateMetadata;
use Ibexa\Migration\ValueObject\Content\Field;
use Ibexa\Migration\ValueObject\Content\Location;
use Ibexa\Migration\ValueObject\Step\ContentCreateStep;
use Ibexa\Tests\Bundle\Migration\Serializer\AbstractSerializationTestCase;
use Traversable;
use Webmozart\Assert\Assert;

final class ContentCreateStepNormalizerTest extends AbstractSerializationTestCase
{
    public function provideForSerialization(): iterable
    {
        $content = new Content([
            'versionInfo' => new VersionInfo([
                'contentInfo' => new ContentInfo([
                    'remoteId' => 'foo',
                    'mainLanguage' => new Language([
                        'languageCode' => 'en-US',
                    ]),
                    'alwaysAvailable' => true,
                    'sectionId' => 1,
                    'section' => new Section([
                        'id' => 1,
                        'identifier' => 'foo_section_identifier',
                    ]),
                    'publishedDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'modificationDate' => new \DateTime('2020-10-30T11:40:09+00:00'),
                    'mainLocation' => new APILocation([
                        'parentLocationId' => 1,
                        'hidden' => true,
                        'sortField' => 1,
                        'sortOrder' => 1,
                        'priority' => 1,
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'foo',
            ]),
        ]);

        $fields = [];

        $location = $content->contentInfo->getMainLocation();
        Assert::notNull($location);
        $data = [
            new ContentCreateStep(
                CreateMetadata::createFromContent($content),
                Location::createFromValueObject($location),
                $fields,
            ),
        ];

        $expectedResult = self::loadFile(__DIR__ . '/content--create/normalize/content--create.yaml');

        yield [
            $data,
            $expectedResult,
        ];

        $introValue = <<<VALUE
<?xml version="1.0"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" version="5.0-variant ezpublish-1.0">
    <para>If you&#x2019;re planning to invest in a new patio set or looking for a fresh way to rearrange your current furniture, take a peek at these eight setups, each offering an invitation to get outside. All include combinations of outdoor furniture &#x2014; cafe tables, chairs, benches, lounges and more &#x2014; and at least one other garden element that makes the scene that much more enticing.</para>
</section>
VALUE;

        $descriptionValue = <<<VALUE
<?xml version="1.0"?>
<section xmlns="http://docbook.org/ns/docbook" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:ezxhtml="http://ibexa.co/xmlns/dxp/docbook/xhtml" xmlns:ezcustom="http://ibexa.co/xmlns/dxp/docbook/custom" version="5.0-variant ezpublish-1.0">
    <para>1. Pattern Play in Southern California</para>
    <para>The fabulous patterned tile floor may be the first thing that catches your eye in this outdoor room by Genius Home Landscape in Orange County. However, as soon as the sun goes down, it&#x2019;s the enveloping warmth of the fireplace and the &#x2014; surprising &#x2014; heated bench that would draw you in.</para>
    <para>The curved bench, made by Galanter &amp; Jones from cast stone and powder-coated stainless steel, can be plugged in and cranked up to 120 degrees Fahrenheit, making a seat away from the hearth just as toasty as one close to the fireplace.</para>
    <para>A small, portable gas-burning fire pit is much less expensive than a permanent fireplace or outdoor hearth, and it offers the same warmth and anchoring factor.</para>
    <para>Cozy up outdoor furniture by laying down sheepskins (faux or real) on the seats and draping throws on the backs.</para>
    <para>&#xA0;</para>
    <para>2. Vine-Covered Terrace in Seattle</para>
    <para>Covered by a trellis dripping in wisteria, this seating area by AHBL almost feels as if it&#x2019;s in a magical world.</para>
    <para>The furniture (a Richard Schultz set, part of the 1966 collection by Knoll) and built-in fireplace are simple and clean-lined, bringing focus to the garden beds filled with purple allium, lady&#x2019;s mantle and, of course, the hanging blooms of the white wisteria.</para>
    <para>Add more romance to any patio space with the addition of a shade trellis covered in flowering vines.</para>
    <para>&#xA0;</para>
    <para>3. Built-In Benches in London</para>
    <para>Built-in seating that flanks an outdoor wood-burning fireplace makes an inviting space in this London backyard by Gotham Interiors. Keeping the benches around the edges saves floor space and makes the patio feel more open and expansive. Printed Moroccan-style throw pillows and a sheepskin add color and softness to the wooden bench seats.</para>
    <para>Built-in benches can also be used as one side of seating for an outdoor table, if you&#x2019;d like to turn a built-in lounge seating area into a dining spot later on.</para>
    <para>&#xA0;</para>
    <para>4. Gravel Patio in Palo Alto</para>
    <para>The furniture in this San Francisco Bay Area backyard by Randy Thueme Design, complete with a gravel terrace bordered by olive trees and crisscrossing cafe lights overhead, is practically arranged as an open-plan living room.</para>
    <para>An outdoor kitchen and pizza oven sit on one side of the terrace, with an outdoor dining table for six within easy distance for bringing plates to the table. On the other side of the patio, two accent chairs drawn up to the fire feature are almost the outdoor equivalent of cushy armchairs next to a hearth.</para>
    <para>This space feels welcoming for a number of reasons &#x2014; the open layout and good-looking furniture, for starters &#x2014; but it&#x2019;s the twinkling glow of the cafe lights that really sets the mood.</para>
    <para>If you&#x2019;re looking for string lights for your patio, skip the widely available ones at home stores and invest in waterproof hospitality-quality ones that will last for a decade. These types of string lights, usually sold by outdoor lighting stores, often come with the cord and bulbs separately.</para>
    <para>&#xA0;</para>
    <para>5. Tropical Terrace in Toronto</para>
    <para>Surrounded by vine-covered walls and colorful summer containers, this secluded backyard terrace by Terra Firma Design in Toronto is a leafy retreat. The patio has just enough room for a seating area around a coffee table, a chaise lounge (out of view), a round dining table for four and a small sideboard staging table.</para>
    <para>Bold prints, red accents and dark furniture materials help set an almost tropical feel.</para>
    <para>In warm and sunny climates, there&#x2019;s hardly anything more inviting than having a seat in the shade with a cool drink. In this St. Helena garden by Katharine Webster, a pergola topped with a lattice of willow branches casts light shade over an outdoor lounge of cushy sofas, armchairs and ottomans.</para>
    <para>Even though this urban garden is small in square footage, the terraced design and separate seating areas for different uses &#x2014; one for lounging and one for outdoor dining &#x2014; create a dynamic setup for outdoor living. Together, the simple wood-and-white slingback chairs and whitewashed metal bistro dining furniture set a casual, country-style feel. Combined with the lavender plants, the space feels like a slice of Provence in London.</para>
</section>
VALUE;

        $content = new Content([
            'versionInfo' => new VersionInfo([
                'creatorId' => 14,
                'contentInfo' => new ContentInfo([
                    'remoteId' => '638554c7fb4bfa399004005668323567',
                    'mainLanguage' => new Language([
                        'languageCode' => 'eng-GB',
                    ]),
                    'ownerId' => 14,
                    'alwaysAvailable' => true,
                    'sectionId' => 1,
                    'section' => new Section([
                        'id' => 1,
                        'identifier' => 'foo_section_identifier',
                    ]),
                    'publishedDate' => new \DateTime('2020-09-17T13:23:15+00:00'),
                    'modificationDate' => new \DateTime('2020-09-17T13:23:15+00:00'),
                    'mainLocation' => new APILocation([
                        'remoteId' => 'd46ae323dbc18b34c9fa218485d4e26a',
                        'parentLocationId' => 538,
                        'hidden' => false,
                        'sortField' => 1,
                        'sortOrder' => 1,
                        'priority' => 0,
                    ]),
                ]),
            ]),
            'contentType' => new ContentType([
                'identifier' => 'blog_post',
            ]),
        ]);

        $expectedResult = self::loadFile(__DIR__ . '/content--create/normalize/content--create--extended.yaml');

        $fields = [
            Field::createFromArray([
                'fieldDefIdentifier' => 'name',
                'languageCode' => 'eng-GB',
                'value' => '5 Patio Arrangements to Inspire Your Outdoor Room',
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'intro',
                'languageCode' => 'eng-GB',
                'value' => $introValue,
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'description',
                'languageCode' => 'eng-GB',
                'value' => $descriptionValue,
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'image',
                'languageCode' => 'eng-GB',
                'value' => null,
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'publication_date',
                'languageCode' => 'eng-GB',
                'value' => '2020-04-08T16:33:00+0000',
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'author',
                'languageCode' => 'eng-GB',
                'value' => (string) new FieldType\Author\Value([
                    new FieldType\Author\Author([
                        'id' => '1',
                        'name' => 'Yura Rajzer',
                        'email' => 'yura.rajzer@example.com',
                    ]),
                ]),
            ]),
            Field::createFromArray([
                'fieldDefIdentifier' => 'authors_position',
                'languageCode' => 'eng-GB',
                'value' => 'Content Marketing Editor',
            ]),
        ];

        $location = $content->contentInfo->getMainLocation();
        Assert::notNull($location);

        $data = [
            new ContentCreateStep(
                CreateMetadata::createFromContent($content),
                Location::createFromValueObject($location),
                $fields,
                [
                    new ReferenceDefinition(
                        'ref__content__blog_post__5 patio arrangements to inspire your outdoor room',
                        'content_id',
                    ),
                    new ReferenceDefinition(
                        'ref_location__blog_post__5 patio arrangements to inspire your outdoor room',
                        'location_id',
                    ),
                ]
            ),
        ];

        yield [
            $data,
            $expectedResult,
        ];
    }

    public function provideForDeserialization(): iterable
    {
        $source = self::loadFile(__DIR__ . '/content--create/denormalize/content--create.yaml');

        $expectation = static function ($deserialized): void {
            self::assertIsIterable($deserialized);
            if ($deserialized instanceof Traversable) {
                $deserialized = iterator_to_array($deserialized);
            }
            self::assertContainsOnlyInstancesOf(ContentCreateStep::class, $deserialized);
            self::assertCount(1, $deserialized);
            [$expectedObject] = $deserialized;

            self::assertInstanceOf(ContentCreateStep::class, $expectedObject);
            self::assertInstanceOf(CreateMetadata::class, $expectedObject->metadata);
            self::assertInstanceOf(Location::class, $expectedObject->location);
        };

        yield [
            $source,
            $expectation,
        ];
    }
}

class_alias(ContentCreateStepNormalizerTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Serializer\Normalizer\Step\ContentCreateStepNormalizerTest');
