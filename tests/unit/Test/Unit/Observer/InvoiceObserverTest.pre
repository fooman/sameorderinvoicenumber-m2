<?php

namespace Fooman\SameOrderInvoiceNumber\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class InvoiceObserverTest extends \PHPUnit\Framework\TestCase
{
    const TEST_STORE_ID = 1;
    const TEST_PREFIX = 'INV-';

    /** @var InvoiceObserver */
    protected $object;

    /** @var ObjectManager */
    protected $objectManager;


    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    /**
     * @param     $orderIncrement
     * @param int $existingInvoices
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getInvoiceCollectionMock($orderIncrement, $existingInvoices = 0)
    {
        $invoiceCollectionMock = $this->createPartialMock(
            '\Magento\Sales\Model\ResourceModel\Order\Invoice\Collection',
            ['getSize', 'getIterator']
        );
        $invoiceCollectionMock->expects($this->atLeastOnce())
            ->method('getSize')
            ->will($this->returnValue($existingInvoices));

        $items = [];

        switch ($existingInvoices) {
            case 2:
                $invoiceMock = $this->createPartialMock(
                    'Magento\Sales\Model\Order\Invoice',
                    ['getIncrementId']
                );
                $invoiceMock->expects($this->any())
                    ->method('getIncrementId')
                    ->willReturn($orderIncrement . '-1');
                $items[1] = $invoiceMock;
            //no break intentionally
            case 1:
                $invoiceMock = $this->createPartialMock(
                    'Magento\Sales\Model\Order\Invoice',
                    ['getIncrementId']
                );
                $invoiceMock->expects($this->any())
                    ->method('getIncrementId')
                    ->willReturn($orderIncrement);
                $items[0] = $invoiceMock;
                break;
        }

        $invoiceCollectionMock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($items));

        return $invoiceCollectionMock;
    }

    /**
     * @param $orderIncrement
     * @param $invoiceMemoCollectionMock
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getInvoiceMock($orderIncrement, $invoiceMemoCollectionMock)
    {
        //Mock Order
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods(['getIncrementId', 'getStoreId', 'getInvoiceCollection'])
            ->getMock();

        $orderMock->expects($this->any())
            ->method('getIncrementId')
            ->will($this->returnValue($orderIncrement));

        $orderMock->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue(self::TEST_STORE_ID));

        $orderMock->expects($this->any())
            ->method('getInvoiceCollection')
            ->will($this->returnValue($invoiceMemoCollectionMock));


        //Mock Invoice
        $invoiceMock = $this->getMockBuilder('Magento\Sales\Model\Order\Invoice')
            ->disableOriginalConstructor()
            ->setMethods(['getOrder', 'getId'])
            ->getMock();

        $invoiceMock->expects($this->any())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));

        $invoiceMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        return $invoiceMock;
    }

    /**
     * @dataProvider salesOrderInvoiceSaveBeforeDataProvider
     *
     * @param $input
     * @param $expected
     */
    public function testSalesOrderInvoiceSaveBefore($input, $expected)
    {
        $this->object = $this->objectManager->getObject(
            'Fooman\SameOrderInvoiceNumber\Observer\InvoiceObserver',
            [
                'scopeConfig' => $this->getScopeConfigMock()
            ]
        );

        $invoiceMock = $this->getInvoiceMock(
            $input['order_increment_id'],
            $this->getInvoiceCollectionMock($input['order_increment_id'], $input['existing_invoices'])
        );

        //Mock Observer
        /** @var \Magento\Framework\Event\Observer $observer */
        $observer = $this->createPartialMock('Magento\Framework\Event\Observer', ['getInvoice']);
        $observer->expects($this->once())
            ->method('getInvoice')
            ->will($this->returnValue($invoiceMock));


        //Execute Observer
        $this->object->execute($observer);

        $this->assertEquals($expected, $invoiceMock->getIncrementId());
    }

    /**
     * @dataProvider salesOrderInvoiceSaveBeforeDataProvider
     *
     * @param $input
     * @param $expected
     */
    public function testSalesOrderInvoiceSaveBeforeWithPrefix($input, $expected)
    {
        $this->object = $this->objectManager->getObject(
            'Fooman\SameOrderInvoiceNumber\Observer\InvoiceObserver',
            [
                'scopeConfig' => $this->getScopeConfigMock(true)
            ]
        );

        $invoiceMock = $this->getInvoiceMock(
            $input['order_increment_id'],
            $this->getInvoiceCollectionMock(
                self::TEST_PREFIX . $input['order_increment_id'],
                $input['existing_invoices']
            )
        );

        //Mock Observer
        $observer = $this->createPartialMock('Magento\Framework\Event\Observer', ['getInvoice']);
        $observer->expects($this->once())
            ->method('getInvoice')
            ->will($this->returnValue($invoiceMock));


        //Execute Observer
        $this->object->execute($observer);

        $this->assertEquals(self::TEST_PREFIX . $expected, $invoiceMock->getIncrementId());
    }


    /**
     * @return array
     */
    public function salesOrderInvoiceSaveBeforeDataProvider()
    {
        return [
            [
                'input'          => [
                    'order_increment_id' => '100000015',
                    'existing_invoices'  => 0
                ],
                'expectedResult' => '100000015',
            ],
            [
                'input'          => [
                    'order_increment_id' => '200000001',
                    'existing_invoices'  => 0
                ],
                'expectedResult' => '200000001',
            ],
            [
                'input'          => [
                    'order_increment_id' => 'TEST--001',
                    'existing_invoices'  => 0
                ],
                'expectedResult' => 'TEST--001',
            ],
            [
                'input'          => [
                    'order_increment_id' => '100000015',
                    'existing_invoices'  => 1
                ],
                'expectedResult' => '100000015-1',
            ],
            [
                'input'          => [
                    'order_increment_id' => '100000015',
                    'existing_invoices'  => 2
                ],
                'expectedResult' => '100000015-2',
            ]
        ];
    }

    /**
     * @param bool $withPrefixes
     *
     * @return \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected function getScopeConfigMock($withPrefixes = false)
    {
        if ($withPrefixes) {
            $scopeConfigMock = $this->createMock('Magento\Framework\App\Config\ScopeConfigInterface');

            $scopeConfigMock->expects($this->any())
                ->method('getValue')
                ->with(
                    'sameorderinvoicenumber/settings/invoiceprefix',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    self::TEST_STORE_ID
                )
                ->will($this->returnValue(self::TEST_PREFIX));
        } else {
            $scopeConfigMock = $this->createMock('Magento\Framework\App\Config\ScopeConfigInterface');
        }
        return $scopeConfigMock;
    }
}
