<?php
/**
 * @author     Kristof Ringleff, Fooman
 * @package    Fooman_SameOrderInvoiceNumber
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Fooman\SameOrderInvoiceNumber\Observer;

class CreditmemoObserver extends AbstractObserver
{

    /**
     * path for prefix config setting
     *
     * @var string
     */
    protected $prefixConfigPath = 'sameorderinvoicenumber/settings/creditmemoprefix';

    /**
     * @param $order
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection
     */
    public function getCollection($order)
    {
        return $order->getCreditmemosCollection();
    }

    /**
     * change the creditmemo increment to the order increment id
     * only affects creditmemos without id (=new creditmemos)
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->assignIncrement($observer->getCreditmemo());
    }
}
