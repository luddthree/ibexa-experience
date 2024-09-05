<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ConnectorQualifio\Service;

use Ibexa\Contracts\ConnectorQualifio\QualifioServiceInterface;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;

final class QualifioServiceTest extends IbexaKernelTestCase
{
    private QualifioServiceInterface $qualifioService;

    protected function setUp(): void
    {
        $qualifioService = self::getContainer()->get(QualifioServiceInterface::class);
        assert($qualifioService instanceof QualifioServiceInterface);
        $this->qualifioService = $qualifioService;
    }

    public function testGetCampaignData(): void
    {
        $campaign = $this->qualifioService->getQualifioCampaign(1265523);

        $expected = [
            'channelId' => '85A00D4C-9063-4CA0-B855-B7A4A918D43B',
            'website' => [
                'id' => 15330,
                'name' => 'Ibexa.co',
            ],
            'channel' => 'WIDGET',
            'id' => 1265524,
            'dateCreated' => '2023-07-12T07:43:42.543Z',
            'dateLastModified' => '2023-07-12T09:54:17.257Z',
            'campaign' => [
                'campaignId' => 1265523,
                'campaignTitle' => 'The Movie quiz',
                'campaignType' => 'Quizz ou concours identifié',
            ],
            'schedule' => [
                'startDate' => '2021-12-16T00:00:00.000Z',
                'endDate' => '2090-12-31T23:59:00.000Z',
                'hourlyLimitation' => false,
            ],
            'integration' => [
                'javascript' => <<<JAVASCRIPT
                    <div id="qualifio_insert_place_1265524" class="qualifio_iframe_wrapper"></div>
                                      <script type="text/javascript">
                                        (function(b,o,n,u,s){
                                          var a,t;a=b.createElement(u);
                                          a.async=1;a.src=s;t=b.getElementsByTagName(u)[0];
                                          t.parentNode.insertBefore(a,t);o[n]=o[n]||[]})
                                        (document,window,'_qual_async','script','//ibexa.qualifioapp.com/kit/qualp.2.min.js');
                                        _qual_async.push(['createIframe', 'qualifio_insert_place_1265524', 'ibexa.qualifioapp.com',
                                        '20', '85A00D4C-9063-4CA0-B855-B7A4A918D43B', '100%', '1200', '', '', '', 'max-width:810px;margin:0 auto;']);
                                      </script>
                    JAVASCRIPT,
                'webview' => 'https://ibexa.qualifioapp.com/20/85A00D4C-9063-4CA0-B855-B7A4A918D43B/v1.cfm?id=85A00D4C-9063-4CA0-B855-B7A4A918D43B',
                'html' => <<<HTML
                    <iframe src="//ibexa.qualifioapp.com/20/85A00D4C-9063-4CA0-B855-B7A4A918D43B/v1.cfm?id=85A00D4C-9063-4CA0-B855-B7A4A918D43B"
                        id="qualifio1265524" class="qualifio_iframe_tag" width="100%" height="1200"
                        scrolling="auto" frameborder="0" hspace="0" vspace="0" style="overflow-x:hidden;max-width: 1200px;">

                    HTML . '    ', // Response contains trailing whitespace
                ],
            ];
        self::assertSame($expected, $campaign);
    }

    public function testGetChannels(): void
    {
        $channels = $this->qualifioService->getQualifioChannels();

        self::assertCount(16, $channels);

        $lastSeenStartDate = null;
        foreach ($channels as $channel) {
            self::assertIsArray($channel);
            self::assertShape($channel);

            self::assertArrayHasKey('schedule', $channel);
            self::assertArrayHasKey('startDate', $channel['schedule']);
            if ($lastSeenStartDate !== null) {
                self::assertTrue(
                    strtotime($lastSeenStartDate) <= strtotime($channel['schedule']['startDate']),
                    sprintf('%s is later than %s', $lastSeenStartDate, $channel['schedule']['startDate']),
                );
            }
            $lastSeenStartDate = $channel['schedule']['startDate'];
        }
    }

    /**
     * @param array<mixed> $channel
     */
    private static function assertShape(array $channel): void
    {
        self::assertArrayHasKey('channelId', $channel);
        self::assertIsString($channel['channelId']);

        self::assertArrayHasKey('website', $channel);
        self::assertIsArray($channel['website']);
        self::assertArrayHasKey('id', $channel['website']);
        self::assertIsInt($channel['website']['id']);
        self::assertArrayHasKey('name', $channel['website']);
        self::assertIsString($channel['website']['name']);

        self::assertArrayHasKey('id', $channel);
        self::assertIsInt($channel['id']);

        self::assertArrayHasKey('dateCreated', $channel);
        self::assertIsString($channel['dateCreated']);

        self::assertArrayHasKey('dateLastModified', $channel);
        self::assertIsString($channel['dateLastModified']);

        self::assertArrayHasKey('campaign', $channel);
        self::assertIsArray($channel['campaign']);
        self::assertArrayHasKey('campaignId', $channel['campaign']);
        self::assertIsInt($channel['campaign']['campaignId']);
        self::assertArrayHasKey('campaignTitle', $channel['campaign']);
        self::assertIsString($channel['campaign']['campaignTitle']);
        self::assertArrayHasKey('campaignType', $channel['campaign']);
        self::assertIsString($channel['campaign']['campaignType']);

        self::assertArrayHasKey('schedule', $channel);
        self::assertIsArray($channel['schedule']);
        self::assertArrayHasKey('startDate', $channel['schedule']);
        self::assertIsString($channel['schedule']['startDate']);
        self::assertArrayHasKey('endDate', $channel['schedule']);
        self::assertIsString($channel['schedule']['endDate']);
        self::assertArrayHasKey('hourlyLimitation', $channel['schedule']);
        self::assertIsBool($channel['schedule']['hourlyLimitation']);

        self::assertArrayHasKey('integration', $channel);
        self::assertIsArray($channel['integration']);
        self::assertArrayHasKey('javascript', $channel['integration']);
        self::assertIsString($channel['integration']['javascript']);
        self::assertArrayHasKey('webview', $channel['integration']);
        self::assertIsString($channel['integration']['webview']);
        self::assertArrayHasKey('html', $channel['integration']);
        self::assertIsString($channel['integration']['html']);
    }
}
