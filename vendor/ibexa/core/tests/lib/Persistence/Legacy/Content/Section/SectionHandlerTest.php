<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\Core\Persistence\Legacy\Content\Section;

use Ibexa\Contracts\Core\Persistence\Content\Section;
use Ibexa\Core\Persistence\Legacy\Content\Section\Gateway;
use Ibexa\Core\Persistence\Legacy\Content\Section\Handler;
use Ibexa\Tests\Core\Persistence\Legacy\TestCase;

/**
 * @covers \Ibexa\Core\Persistence\Legacy\Content\Section\Handler
 */
class SectionHandlerTest extends TestCase
{
    /**
     * Section handler.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Section\Handler
     */
    protected $sectionHandler;

    /**
     * Section gateway mock.
     *
     * @var \Ibexa\Core\Persistence\Legacy\Content\Section\Gateway
     */
    protected $gatewayMock;

    public function testCreate()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('insertSection')
            ->with(
                $this->equalTo('New Section'),
                $this->equalTo('new_section')
            )->will($this->returnValue(23));

        $sectionRef = new Section();
        $sectionRef->id = 23;
        $sectionRef->name = 'New Section';
        $sectionRef->identifier = 'new_section';

        $result = $handler->create('New Section', 'new_section');

        $this->assertEquals(
            $sectionRef,
            $result
        );
    }

    public function testUpdate()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('updateSection')
            ->with(
                $this->equalTo(23),
                $this->equalTo('New Section'),
                $this->equalTo('new_section')
            );

        $sectionRef = new Section();
        $sectionRef->id = 23;
        $sectionRef->name = 'New Section';
        $sectionRef->identifier = 'new_section';

        $result = $handler->update(23, 'New Section', 'new_section');

        $this->assertEquals(
            $sectionRef,
            $result
        );
    }

    public function testLoad()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadSectionData')
            ->with(
                $this->equalTo(23)
            )->will(
                $this->returnValue(
                    [
                        [
                            'id' => '23',
                            'identifier' => 'new_section',
                            'name' => 'New Section',
                        ],
                    ]
                )
            );

        $sectionRef = new Section();
        $sectionRef->id = 23;
        $sectionRef->name = 'New Section';
        $sectionRef->identifier = 'new_section';

        $result = $handler->load(23);

        $this->assertEquals(
            $sectionRef,
            $result
        );
    }

    public function testLoadAll()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
        ->method('loadAllSectionData')
        ->will(
            $this->returnValue(
                [
                    [
                        'id' => '23',
                        'identifier' => 'new_section',
                        'name' => 'New Section',
                    ],
                    [
                        'id' => '46',
                        'identifier' => 'new_section2',
                        'name' => 'New Section2',
                    ],
                ]
            )
        );

        $sectionRef = new Section();
        $sectionRef->id = 23;
        $sectionRef->name = 'New Section';
        $sectionRef->identifier = 'new_section';

        $sectionRef2 = new Section();
        $sectionRef2->id = 46;
        $sectionRef2->name = 'New Section2';
        $sectionRef2->identifier = 'new_section2';

        $result = $handler->loadAll();

        $this->assertEquals(
            [$sectionRef, $sectionRef2],
            $result
        );
    }

    public function testLoadByIdentifier()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('loadSectionDataByIdentifier')
            ->with(
                $this->equalTo('new_section')
            )->will(
                $this->returnValue(
                    [
                        [
                            'id' => '23',
                            'identifier' => 'new_section',
                            'name' => 'New Section',
                        ],
                    ]
                )
            );

        $sectionRef = new Section();
        $sectionRef->id = 23;
        $sectionRef->name = 'New Section';
        $sectionRef->identifier = 'new_section';

        $result = $handler->loadByIdentifier('new_section');

        $this->assertEquals(
            $sectionRef,
            $result
        );
    }

    public function testDelete()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('countContentObjectsInSection')
            ->with($this->equalTo(23))
            ->will($this->returnValue(0));

        $gatewayMock->expects($this->once())
            ->method('deleteSection')
            ->with(
                $this->equalTo(23)
            );

        $result = $handler->delete(23);
    }

    public function testDeleteFailure()
    {
        $this->expectException(\RuntimeException::class);

        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('countContentObjectsInSection')
            ->with($this->equalTo(23))
            ->will($this->returnValue(2));

        $gatewayMock->expects($this->never())
            ->method('deleteSection');

        $result = $handler->delete(23);
    }

    public function testAssign()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('assignSectionToContent')
            ->with(
                $this->equalTo(23),
                $this->equalTo(42)
            );

        $result = $handler->assign(23, 42);
    }

    public function testPoliciesCount()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('countPoliciesUsingSection')
            ->with(
                $this->equalTo(1)
            )
            ->will(
                $this->returnValue(7)
            );

        $result = $handler->policiesCount(1);
    }

    public function testCountRoleAssignmentsUsingSection()
    {
        $handler = $this->getSectionHandler();

        $gatewayMock = $this->getGatewayMock();

        $gatewayMock->expects($this->once())
            ->method('countRoleAssignmentsUsingSection')
            ->with(
                $this->equalTo(1)
            )
            ->will(
                $this->returnValue(0)
            );

        $handler->countRoleAssignmentsUsingSection(1);
    }

    /**
     * Returns the section handler to test.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Section\Handler
     */
    protected function getSectionHandler()
    {
        if (!isset($this->sectionHandler)) {
            $this->sectionHandler = new Handler(
                $this->getGatewayMock()
            );
        }

        return $this->sectionHandler;
    }

    /**
     * Returns a mock for the section gateway.
     *
     * @return \Ibexa\Core\Persistence\Legacy\Content\Section\Gateway
     */
    protected function getGatewayMock()
    {
        if (!isset($this->gatewayMock)) {
            $this->gatewayMock = $this->getMockForAbstractClass(Gateway::class);
        }

        return $this->gatewayMock;
    }
}

class_alias(SectionHandlerTest::class, 'eZ\Publish\Core\Persistence\Legacy\Tests\Content\Section\SectionHandlerTest');
