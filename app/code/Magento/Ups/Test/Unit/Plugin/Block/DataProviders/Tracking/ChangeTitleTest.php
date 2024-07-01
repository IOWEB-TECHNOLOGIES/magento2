<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Ups\Test\Unit\Plugin\Block\DataProviders\Tracking;

use Magento\Ups\Model\Carrier;
use Magento\Ups\Plugin\Block\DataProviders\Tracking\ChangeTitle;
use Magento\Framework\Phrase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Shipping\Block\DataProviders\Tracking\DeliveryDateTitle;
use Magento\Shipping\Model\Tracking\Result\Status;
use Magento\Ups\Model\Carrier as UpsCarrier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit Test for @see ChangeTitle
 */
class ChangeTitleTest extends TestCase
{
    /**
     * @var ChangeTitle|MockObject
     */
    private $plugin;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $this->plugin = $objectManagerHelper->getObject(ChangeTitle::class);
    }

    /**
     * Check if DeliveryDateTitle was changed if the carrier is UPS
     *
     * @param string $carrierCode
     * @param string $originalResult
     * @param Phrase|string $finalResult
     * @dataProvider testAfterGetTitleDataProvider
     */
    public function testAfterGetTitle(string $carrierCode, string $originalResult, $finalResult)
    {
        /** @var DeliveryDateTitle|MockObject $subjectMock */
        $subjectMock = $this->getMockBuilder(DeliveryDateTitle::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var Status|MockObject $trackingStatusMock */
        $trackingStatusMock = $this->getMockBuilder(Status::class)
            ->disableOriginalConstructor()
            ->addMethods(['getCarrier'])
            ->getMock();
        $trackingStatusMock->expects($this::once())
            ->method('getCarrier')
            ->willReturn($carrierCode);

        $actual = $this->plugin->afterGetTitle($subjectMock, $originalResult, $trackingStatusMock);

        $this->assertEquals($finalResult, $actual);
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function testAfterGetTitleDataProvider(): array
    {
        return [
            [Carrier::CODE, 'Original Title', __('Status Updated On:')],
            ['not-fedex', 'Original Title', 'Original Title'],
        ];
    }
}
