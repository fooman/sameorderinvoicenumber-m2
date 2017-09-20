<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestStep;

use Magento\Mtf\Client\Locator;

/**
 * Create credit memo from order on backend.
 */
class CreateCreditMemoStep extends \Magento\Sales\Test\TestStep\CreateCreditMemoStep
{

    public function run()
    {
        $this->orderIndex->open();
        $this->orderIndex->getSalesOrderGrid()->searchAndOpen(['id' => $this->order->getId()]);
        $refundsData = $this->order->getRefund() !== null ? $this->order->getRefund() : ['refundData' => []];
        foreach ($refundsData as $refundData) {
/*            $this->orderCreditMemoNew->getFormBlock()->waitForElementVisible(
                '#creditmemo_item_container',
                Locator::SELECTOR_CSS
            );*/
            $this->salesOrderView->getPageActions()->orderCreditMemo();
            $this->orderCreditMemoNew->getFormBlock()->fillFormData($refundData);
            $this->orderCreditMemoNew->getFormBlock()->submit();
        }

        return [
            'ids' => ['creditMemoIds' => $this->getCreditMemoIds()]
        ];
    }

    protected function getCreditMemoIds()
    {
        $orderForm = $this->salesOrderView->getOrderForm();
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_creditmemos', Locator::SELECTOR_CSS);
        sleep(2);
        $orderForm->openTab('creditmemos');
        $orderForm->waitForElementVisible('#sales_order_view_tabs_order_creditmemos_content', Locator::SELECTOR_CSS);
        return $orderForm->getTab('creditmemos')->getGridBlock()->getIds();
    }
}
