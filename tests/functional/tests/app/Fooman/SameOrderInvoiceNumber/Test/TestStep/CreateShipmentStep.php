<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestStep;

use Magento\Mtf\Client\Locator;

class CreateShipmentStep extends \Magento\Sales\Test\TestStep\CreateShipmentStep
{

    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $this->salesOrderView->getOrderForm()->waitForElementVisible(
            '#order_ship', Locator::SELECTOR_CSS
        );
        $this->salesOrderView->getPageActions()->ship();
        if (!empty($this->data)) {
            $this->orderShipmentNew->getFormBlock()->fillData($this->data, $this->order->getEntityId()['products']);
        }
        $this->orderShipmentNew->getFormBlock()->submit();

        return ['shipmentIds' => $this->getShipmentIds()];
    }

    public function getShipmentIds()
    {
        $orderForm = $this->salesOrderView->getOrderForm();
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_shipments', Locator::SELECTOR_CSS);
        sleep(2);
        $orderForm->openTab('shipments');
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_shipments_content', Locator::SELECTOR_CSS);
        return $orderForm->getTab('shipments')->getGridBlock()->getIds();
    }
}
