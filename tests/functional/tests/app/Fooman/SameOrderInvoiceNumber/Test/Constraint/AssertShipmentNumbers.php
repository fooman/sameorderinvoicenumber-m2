<?php

namespace Fooman\SameOrderInvoiceNumber\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Shipping\Test\Page\Adminhtml\ShipmentIndex;

/**
 * Assert that shipment is present in the shipments grid
 */
class AssertShipmentNumbers extends AbstractConstraint
{
    /**
     * Assert that shipment is present in the shipments grid
     *
     * @param ShipmentIndex $shipmentIndex
     * @param array         $ids
     *
     * @return void
     */
    public function processAssert(
        ShipmentIndex $shipmentIndex,
        array $ids
    ) {
        foreach ($ids as $orderId => $shipmentIds) {
            $shipmentIndex->open();
            $grid = $shipmentIndex->getShipmentsGrid();

            foreach ($shipmentIds as $shipmentId) {
                $filter = [
                    'id'       => $shipmentId,
                    'order_id' => $orderId,
                ];

                $grid->search($filter);

                \PHPUnit_Framework_Assert::assertTrue(
                    $grid->isRowVisible($filter, false, true),
                    'Shipment is absent on shipments grid.'
                );
            }
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Shipment is present on shipments grid.';
    }
}
