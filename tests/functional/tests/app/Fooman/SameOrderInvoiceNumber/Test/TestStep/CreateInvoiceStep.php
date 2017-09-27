<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestStep;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\OrderIndex;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceNew;
use Magento\Sales\Test\Page\Adminhtml\OrderInvoiceView;
use Magento\Sales\Test\Page\Adminhtml\SalesOrderView;
use Magento\Shipping\Test\Page\Adminhtml\OrderShipmentView;
use Magento\Mtf\TestStep\TestStepInterface;

/**
 * Create invoice from order on backend.
 */
class CreateInvoiceStep implements TestStepInterface
{
    /**
     * Orders Page.
     *
     * @var OrderIndex
     */
    private $orderIndex;

    /**
     * Order View Page.
     *
     * @var SalesOrderView
     */
    private $salesOrderView;

    /**
     * Order New Invoice Page.
     *
     * @var OrderInvoiceNew
     */
    private $orderInvoiceNew;

    /**
     * Order invoice view page.
     *
     * @var OrderInvoiceView
     */
    private $orderInvoiceView;

    /**
     * Order shipment view page.
     *
     * @var OrderShipmentView
     */
    private $orderShipmentView;

    /**
     * OrderInjectable fixture.
     *
     * @var OrderInjectable
     */
    private $order;

    /**
     * Invoice data.
     *
     * @var array|null
     */
    private $data;

    /**
     * Whether Invoice is partial.
     *
     * @var string
     */
    private $isInvoicePartial;

    /**
     * Payment Action.
     *
     * @var string
     */
    private $paymentAction;

    /**
     * Order ID.
     *
     * @var string
     */
    private $orderId;

    /**
     * @construct
     * @param OrderIndex $orderIndex
     * @param SalesOrderView $salesOrderView
     * @param OrderInvoiceNew $orderInvoiceNew
     * @param OrderInvoiceView $orderInvoiceView
     * @param OrderInjectable $order
     * @param OrderShipmentView $orderShipmentView
     * @param array|null $data [optional]
     * @param string $isInvoicePartial [optional]
     * @param string $paymentAction
     * @param string $orderId
     */
    public function __construct(
        OrderIndex $orderIndex,
        SalesOrderView $salesOrderView,
        OrderInvoiceNew $orderInvoiceNew,
        OrderInvoiceView $orderInvoiceView,
        OrderInjectable $order,
        OrderShipmentView $orderShipmentView,
        $data = null,
        $isInvoicePartial = null,
        $paymentAction = 'authorize',
        $orderId = null
    ) {
        $this->orderIndex = $orderIndex;
        $this->salesOrderView = $salesOrderView;
        $this->orderInvoiceNew = $orderInvoiceNew;
        $this->orderInvoiceView = $orderInvoiceView;
        $this->order = $order;
        $this->orderShipmentView = $orderShipmentView;
        $this->data = $data;
        $this->isInvoicePartial = $isInvoicePartial;
        $this->paymentAction = $paymentAction;
        $this->orderId = $orderId;
    }

    /**
     * Create invoice (with shipment optionally) for order in Admin.
     *
     * @return array
     */
    public function run()
    {
        if ($this->paymentAction == 'sale') {
            return null;
        }
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->orderId]);
        $this->salesOrderView->getOrderForm()->waitForElementVisible(
            '#order_invoice', Locator::SELECTOR_CSS
        );
        $this->salesOrderView->getPageActions()->invoice();

        if (!empty($this->data)) {
            $this->orderInvoiceNew->getFormBlock()->fillProductData(
                $this->data,
                $this->order->getEntityId()['products']
            );
            $this->orderInvoiceNew->getFormBlock()->updateQty();
            $this->orderInvoiceNew->getFormBlock()->fillFormData($this->data);
            if (isset($this->isInvoicePartial)) {
                $this->orderInvoiceNew->getFormBlock()->submit();
                $this->salesOrderView->getPageActions()->invoice();
            }
        }
        $this->orderInvoiceNew->getFormBlock()->submit();
        $invoiceIds = $this->getInvoiceIds();
        if (!empty($this->data)) {
            $shipmentIds = $this->getShipmentIds();
        }

        return [
            'ids' => [
                'invoiceIds' => $invoiceIds,
                'shipmentIds' => isset($shipmentIds) ? $shipmentIds : null,
            ]
        ];
    }

    protected function getInvoiceIds()
    {
        $orderForm = $this->salesOrderView->getOrderForm();
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_invoices', Locator::SELECTOR_CSS);
        sleep(2);
        $orderForm->openTab('invoices');
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_invoices_content', Locator::SELECTOR_CSS);
        return $orderForm->getTab('invoices')->getGridBlock()->getIds();
    }

    protected function getShipmentIds()
    {
        $orderForm = $this->salesOrderView->getOrderForm();
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_shipments', Locator::SELECTOR_CSS);
        sleep(2);
        $orderForm->openTab('shipments');
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_shipments_content', Locator::SELECTOR_CSS);
        return $orderForm->getTab('shipments')->getGridBlock()->getIds();
    }
}
