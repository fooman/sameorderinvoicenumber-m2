<?php
/**
 * @author     Kristof Ringleff, Fooman
 * @package    Fooman_SameOrderInvoiceNumber
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Fooman\SameOrderInvoiceNumber\Observer;

abstract class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * path for prefix config setting
     *
     * @var string
     */
    protected $prefixConfigPath;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param null $storeId
     *
     * @return mixed|string
     */
    public function getPrefixSetting($storeId = null)
    {
        return $this->scopeConfig->getValue(
            $this->prefixConfigPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $order
     *
     * @return \Magento\Sales\Model\ResourceModel\Order\Collection\AbstractCollection
     */
    abstract public function getCollection($order);

    /**
     * @param $entity
     */
    public function assignIncrement($entity)
    {
        if (!$entity->getId()) {
            $order = $entity->getOrder();
            $prefix = $this->getPrefixSetting($order->getStoreId());
            $collection = $this->getCollection($order);
            $prefixedOrderIncrement = $prefix . $order->getIncrementId();
            if ($collection->getSize() == 0) {
                $newNr = $prefixedOrderIncrement;
            } else {
                $maxPostFix = 0;
                foreach ($collection as $item) {
                    $currentPostfix = trim(str_replace($prefixedOrderIncrement, '', $item->getIncrementId()), '-');
                    if (empty($currentPostfix)) {
                        $currentPostfix = 1;
                    } else {
                        $currentPostfix++;
                    }
                    $maxPostFix = max($maxPostFix, $currentPostfix);
                }
                $newNr = $prefix . $order->getIncrementId() . '-' . $maxPostFix;
            }
            $entity->setIncrementId($newNr);
        }
    }
}
