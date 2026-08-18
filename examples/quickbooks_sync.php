<?php
/**
 * DJEP PHP SDK - QuickBooks Sync Example
 *
 * This example shows how to pull payment and expense data
 * for syncing with QuickBooks or other accounting software.
 */

require __DIR__ . '/../autoload.php';

use DJEP\Client;
use DJEP\Exceptions\DJEPException;

$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'));

try {
    // Get all payments using auto-pagination
    echo "=== PAYMENTS ===\n";
    $paymentTotal = 0;
    foreach ($djep->payments->all() as $payment) {
        echo sprintf(
            "  %s | %s | $%.2f | %s\n",
            $payment->clpr_date,
            $payment->client_name ?? 'Unknown',
            $payment->clpr_amount,
            $payment->clpr_method
        );
        $paymentTotal += $payment->clpr_amount;
    }
    echo sprintf("  TOTAL: $%.2f\n\n", $paymentTotal);

    // Get all expenses using auto-pagination
    echo "=== EXPENSES ===\n";
    $expenseTotal = 0;
    foreach ($djep->expenses->all() as $expense) {
        echo sprintf(
            "  %s | %s | $%.2f | %s\n",
            $expense->expense_date ?? '',
            $expense->payee_name ?? 'Unknown',
            $expense->amount ?? 0,
            $expense->category_name ?? 'Uncategorized'
        );
        $expenseTotal += $expense->amount ?? 0;
    }
    echo sprintf("  TOTAL: $%.2f\n\n", $expenseTotal);

    // Get expense categories for chart of accounts mapping
    echo "=== EXPENSE CATEGORIES ===\n";
    $categories = $djep->expenses->categories();
    foreach ($categories->data as $cat) {
        $status = $cat->inactive ? ' (inactive)' : '';
        echo "  {$cat->category}{$status}\n";
    }

} catch (DJEPException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
