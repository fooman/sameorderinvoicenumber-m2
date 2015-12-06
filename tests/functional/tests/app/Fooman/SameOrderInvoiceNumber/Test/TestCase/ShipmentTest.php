<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestCase;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;

class ShipmentTest extends AbstractTest
{

    /**
     * @param int    $orderCount
     * @param int    $shipmentsPerOrderCount
     * @param int    $skipOrderCount
     * @param string $prefix
     *
     * @return array
     */
    public function test($orderCount = 1, $shipmentsPerOrderCount = 1, $skipOrderCount = 0, $prefix = '')
    {
        $this->setPrefix($prefix);

        $shipments = [];
        $orders = $this->createOrders($orderCount, $shipmentsPerOrderCount);

        foreach ($orders as $orderIndex => $order) {
            if ($skipOrderCount > 0 && $orderIndex < $skipOrderCount) {
                continue;
            }

            $this->createInvoice($order);

            if (!isset($shipments[$order->getId()])) {
                $shipments[$order->getId()] = [];
            }

            for ($i = 0; $i < $shipmentsPerOrderCount; $i++) {
                $count = $shipmentsPerOrderCount - 1 - $i;

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

                $this->createShipment($order, $data);

                $shipmentCount = count($shipments[$order->getId()]);

                if ($shipmentCount === 0) {
                    $shipments[$order->getId()][] = $prefix . $order->getId();
                } else {
                    $shipments[$order->getId()][] = $prefix . $order->getId() . '-' . $shipmentCount;
                }
            }
        }

        $this->setPrefix('');

        return ['ids' => $shipments];
    }

    /**
     * @param $prefix
     */
    protected function setPrefix($prefix)
    {
        $this->setPrefixSetting('shipment', $prefix);
    }
}
