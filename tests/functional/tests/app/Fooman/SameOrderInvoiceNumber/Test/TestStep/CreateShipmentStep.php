<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestStep;

use Magento\Mtf\Client\Locator;

class CreateShipmentStep extends \Magento\Sales\Test\TestStep\CreateShipmentStep
{

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
