<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestCase;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;

class InvoiceTest extends AbstractTest
{

    /**
     * @param int    $orderCount
     * @param int    $invoicesPerOrderCount
     * @param int    $skipOrderCount
     * @param string $prefix
     *
     * @return array
     */
    public function test($orderCount = 1, $invoicesPerOrderCount = 1, $skipOrderCount = 0, $prefix = '')
    {
        $this->setPrefix($prefix);

        $invoices = [];
        $orders = $this->createOrders($orderCount, $invoicesPerOrderCount);

        foreach ($orders as $orderIndex => $order) {
            if ($skipOrderCount > 0 && $orderIndex < $skipOrderCount) {
                continue;
            }

            if (!isset($invoices[$order->getId()])) {
                $invoices[$order->getId()] = [];
            }

            for ($i = 0; $i < $invoicesPerOrderCount; $i++) {
                $count = $invoicesPerOrderCount - 1 - $i;

                if ($count < 1) {
                    $itemsData = [];
                } else {
                    $itemsData = array_fill(1, $count, ['qty' => 0]);
                }

                $itemsData = array_merge([0 => ['qty' => 1]], $itemsData);

                if (count($itemsData) === 1) {
                    $data = [];
                } else {
                    $data = ['items_data' => $itemsData];
                }

                $this->createInvoice($order, $data);
                $invoiceCount = count($invoices[$order->getId()]);

                if ($invoiceCount === 0) {
                    $invoices[$order->getId()][] = $prefix . $order->getId();
                } else {
                    $invoices[$order->getId()][] = $prefix . $order->getId() . '-' . $invoiceCount;
                }
            }
        }

        $this->setPrefix('');

        return ['ids' => $invoices];
    }

    /**
     * @param $prefix
     */
    protected function setPrefix($prefix)
    {
        $this->setPrefixSetting('invoice', $prefix);
    }
}
