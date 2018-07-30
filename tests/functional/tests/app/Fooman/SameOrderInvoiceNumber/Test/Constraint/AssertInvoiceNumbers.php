<?php

namespace Fooman\SameOrderInvoiceNumber\Test\Constraint;

use Magento\Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Sales\Test\Page\Adminhtml\InvoiceIndex;

/**
 * Assert that invoice is present in the invoices grid
 */
class AssertInvoiceNumbers extends AbstractConstraint
{
    /**
     * Assert that invoice is present in the invoices grid
     *
     * @param InvoiceIndex $invoiceIndex
     * @param array        $ids
     *
     * @return void
     */
    public function processAssert(
        InvoiceIndex $invoiceIndex,
        array $ids
    ) {
        foreach ($ids as $orderId => $invoiceIds) {
            $invoiceIndex->open();
            $grid = $invoiceIndex->getInvoicesGrid();

            foreach ($invoiceIds as $invoiceId) {
                $filter = [
                    'id'       => $invoiceId,
                    'order_id' => $orderId,
                ];

                $grid->search($filter);

                \PHPUnit\Framework\Assert::assertTrue(
                    $grid->isRowVisible($filter, false, true),
                    'Invoice is absent on invoices grid.'
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
        return 'Invoice is present on invoices grid.';
    }
}
