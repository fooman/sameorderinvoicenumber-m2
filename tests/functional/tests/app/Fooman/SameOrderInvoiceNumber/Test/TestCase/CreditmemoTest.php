<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestCase;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;

class CreditmemoTest extends AbstractTest
{

    /**
     * @param int    $orderCount
     * @param int    $creditmemosPerOrderCount
     * @param int    $skipOrderCount
     * @param string $prefix
     *
     * @return array
     */
    public function test($orderCount = 1, $creditmemosPerOrderCount = 1, $skipOrderCount = 0, $prefix = '')
    {
        $this->setPrefix($prefix);

        $creditmemos = [];
        $orders = $this->createOrders($orderCount, $creditmemosPerOrderCount);

        foreach ($orders as $orderIndex => $order) {
            if ($skipOrderCount > 0 && $orderIndex < $skipOrderCount) {
                continue;
            }

            $this->createInvoice($order);

            if (!isset($creditmemos[$order->getId()])) {
                $creditmemos[$order->getId()] = [];
            }

            for ($i = 0; $i < $creditmemosPerOrderCount; $i++) {
                $count = $creditmemosPerOrderCount - 1 - $i;

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

                $this->createCreditmemo($order, $data);

                $creditmemoCount = count($creditmemos[$order->getId()]);

                if ($creditmemoCount === 0) {
                    $creditmemos[$order->getId()][] = $prefix . $order->getId();
                } else {
                    $creditmemos[$order->getId()][] = $prefix . $order->getId() . '-' . $creditmemoCount;
                }
            }
        }

        $this->setPrefix('');

        return ['ids' => $creditmemos];
    }

    /**
     * @param $prefix
     */
    protected function setPrefix($prefix)
    {
        $this->setPrefixSetting('creditmemo', $prefix);
    }
}
