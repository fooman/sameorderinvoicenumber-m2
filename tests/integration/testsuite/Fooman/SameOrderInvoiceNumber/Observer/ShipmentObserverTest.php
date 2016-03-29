<?php

namespace Fooman\SameOrderInvoiceNumber\Observer;

/**
 * @magentoAppArea      adminhtml
 */
class ShipmentObserverTest extends \PHPUnit_Framework_TestCase
{

    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     */
    public function testShipmentNumberWithoutPrefix()
    {
        $shipment = $this->shipOrder();

        $this->assertEquals('100000001', $shipment->getIncrementId());
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoConfigFixture current_store sameorderinvoicenumber/settings/shipmentprefix SHIP-
     */
    public function testShipmentNumberWithPrefix()
    {
        $shipment = $this->shipOrder();

        $this->assertEquals('SHIP-100000001', $shipment->getIncrementId());
    }

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    protected function shipOrder()
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->objectManager->create('\Magento\Sales\Api\Data\OrderInterface')
            ->load('100000001', 'increment_id');

        /** @var \Magento\Sales\Model\Order\ShipmentFactory $shipmentFactory */
        $shipmentFactory = $this->objectManager->get('\Magento\Sales\Model\Order\ShipmentFactory');

        /** @var \Magento\Sales\Api\Data\ShipmentInterface $shipment */
        $shipment = $shipmentFactory->create($order);

        /** @var \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = $this->objectManager->get('\Magento\Sales\Api\ShipmentRepositoryInterface');
        $shipmentRepository->save($shipment);
        return $shipment;
    }
}
