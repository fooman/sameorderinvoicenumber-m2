<?php

namespace Fooman\SameOrderInvoiceNumber\Test\TestCase;

use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Mtf\TestCase\Injectable;

abstract class AbstractTest extends Injectable
{
    /**
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * @param FixtureFactory $fixtureFactory
     */
    public function __inject(FixtureFactory $fixtureFactory)
    {
        $this->fixtureFactory = $fixtureFactory;
    }

    /**
     * @param int $orderCount
     * @param int $invoicesPerOrderCount
     *
     * @return \Generator
     */
    protected function createOrders($orderCount, $invoicesPerOrderCount)
    {
        for ($i = 0; $i < $orderCount; $i++) {
            yield $this->createOrder($invoicesPerOrderCount);
        }
    }

    /**
     * @param int $qty
     *
     * @return OrderInjectable
     */
    protected function createOrder($qty)
    {
        $products = implode(',', array_fill(0, $qty, 'catalogProductSimple::product_10_dollar'));

        $order = $this->fixtureFactory->createByCode(
            'orderInjectable',
            [
                'dataset' => 'default',
                'data'    => [
                    'entity_id' => [
                        'products' => $products,
                    ]
                ]
            ]
        );

        $order->persist();

        return $order;
    }

    /**
     * @param OrderInjectable $order
     * @param array           $data
     *
     * @return array
     */
    protected function createInvoice(OrderInjectable $order, array $data = [])
    {
        $invoiceIds = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateInvoiceStep',
            [
                'order' => $order,
                'data'  => $data,
            ]
        )->run();

        if (isset($invoiceIds['ids']['invoiceIds'])) {
            return $invoiceIds['ids']['invoiceIds'];
        } else {
            return $invoiceIds['invoiceIds'];
        }
    }

    /**
     * @param OrderInjectable $order
     * @param array           $data
     *
     * @return array
     */
    protected function createShipment(OrderInjectable $order, array $data = [])
    {
        $shipmentIds = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateShipmentStep',
            [
                'order' => $order,
                'data'  => $data,
            ]
        )->run();

        if (isset($shipmentIds['ids']['shipmentIds'])) {
            return $shipmentIds['ids']['shipmentIds'];
        } else {
            return $shipmentIds['shipmentIds'];
        }
    }

    /**
     * @param OrderInjectable $order
     * @param array           $data
     *
     * @return array
     */
    protected function createCreditmemo(OrderInjectable $order, array $data = [])
    {
        $creditmemoIds = $this->objectManager->create(
            'Magento\Sales\Test\TestStep\CreateCreditMemoStep',
            [
                'order' => $order,
                'data'  => $data,
            ]
        )->run();

        if (isset($creditmemoIds['ids']['creditMemoIds'])) {
            return $creditmemoIds['ids']['creditMemoIds'];
        } else {
            return $creditmemoIds['creditMemoIds'];
        }
    }

    /**
     * @param string $type
     * @param string $prefix
     */
    protected function setPrefixSetting($type, $prefix)
    {
        $config = $this->fixtureFactory->createByCode(
            'configData',
            ['data' => [
                'sameorderinvoicenumber/settings/' . $type . 'prefix' => [
                    'scope'    => 'sameorderinvoicenumber',
                    'scope_id' => '0',
                    'label'    => '',
                    'value'    => $prefix,
                ]
            ]]
        );

        $config->persist();
    }
}
