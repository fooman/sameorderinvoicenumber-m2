<?php

namespace Fooman\SameOrderInvoiceNumber\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\CreditMemoIndex;

/**
 * Assert that creditmemo is present in the creditmemos grid
 */
class AssertCreditmemoNumbers extends AbstractConstraint
{
    /**
     * Assert that creditmemo is present in the creditmemos grid
     *
     * @param CreditMemoIndex $creditmemoIndex
     * @param array           $ids
     *
     * @return void
     */
    public function processAssert(
        CreditMemoIndex $creditmemoIndex,
        array $ids
    ) {
        foreach ($ids as $orderId => $creditmemoIds) {
            $creditmemoIndex->open();
            $grid = $creditmemoIndex->getCreditMemoGrid();

            foreach ($creditmemoIds as $creditmemoId) {
                $filter = [
                    'id'       => $creditmemoId,
                    'order_id' => $orderId,
                ];

                $grid->search($filter);

                \PHPUnit_Framework_Assert::assertTrue(
                    $grid->isRowVisible($filter, false, true),
                    'Creditmemo is absent on creditmemos grid.'
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
        return 'Creditmemo is present on creditmemos grid.';
    }
}
