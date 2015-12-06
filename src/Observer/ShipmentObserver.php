<?php
/**
 * @author     Kristof Ringleff, Fooman
 * @package    Fooman_SameOrderInvoiceNumber
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Fooman\SameOrderInvoiceNumber\Observer;

class ShipmentObserver extends AbstractObserver
{

    /**
     * path for prefix config setting
     *
     * @var string
     */
    protected $prefixConfigPath = 'sameorderinvoicenumber/settings/shipmentprefix';

    /**
     * @param $order
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection
     */
    public function getCollection($order)
    {
        return $order->getShipmentsCollection();
    }

    /**
     * change the shipment increment to the order increment id
     * only affects shipments without id (=new shipments)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->assignIncrement($observer->getShipment());
    }
}
