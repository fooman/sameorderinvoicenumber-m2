<?php

namespace Fooman\SameOrderInvoiceNumber\Observer;

/**
 * @magentoAppArea      adminhtml
 */
class InvoiceObserverTest extends \PHPUnit\Framework\TestCase
{

    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     */
    public function testInvoiceNumberWithoutPrefix()
    {
        $invoice = $this->invoiceOrder();

        $this->assertEquals('100000001', $invoice->getIncrementId());
    }

    /**
     * @magentoDataFixture   Magento/Sales/_files/order.php
     * @magentoConfigFixture current_store sameorderinvoicenumber/settings/invoiceprefix INV-
     */
    public function testInvoiceNumberWithPrefix()
    {
        $invoice = $this->invoiceOrder();

        $this->assertEquals('INV-100000001', $invoice->getIncrementId());
    }

    /**
     * @return \Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function invoiceOrder()
    {
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->objectManager->create('\Magento\Sales\Api\Data\OrderInterface')
            ->load('100000001', 'increment_id');

        /** @var \Magento\Sales\Api\InvoiceManagementInterface $invoiceManagement */
        $invoiceManagement = $this->objectManager->get('\Magento\Sales\Api\InvoiceManagementInterface');

        /** @var \Magento\Sales\Api\Data\InvoiceInterface $shipment */
        $invoice = $invoiceManagement->prepareInvoice($order);

        /** @var \Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = $this->objectManager->get('\Magento\Sales\Api\InvoiceRepositoryInterface');
        $invoiceRepository->save($invoice);
        return $invoice;
    }
}
