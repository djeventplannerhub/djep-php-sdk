<?php
/**
 * DJEP PHP SDK - Create Event with New Client
 *
 * This example shows how to create an event with an inline client
 * and demonstrates the financial auto-calculation.
 */

require __DIR__ . '/../autoload.php';

use DJEP\Client;
use DJEP\Exceptions\ValidationException;
use DJEP\Exceptions\DJEPException;

$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'));

try {
    // Create an event with a new client in one call
    $event = $djep->events->create([
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'email' => 'sarah@example.com',
        'cell_phone' => '555-0123',
        'event_date' => '2026-11-15',
        'event_type' => 'Wedding',
        'event_name' => 'Johnson-Williams Wedding',
        'start_time' => '4:00 PM',
        'end_time' => '11:00 PM',
        'guest_count' => '200',
        'status' => 'Tentative',
    ]);

    echo "Event created!\n";
    echo "  Event ID: " . $event->data->eventid . "\n";
    echo "  Client ID: " . $event->data->clientid . "\n";
    echo "  Package Price: $" . $event->data->financials->package_price . "\n";
    echo "  Tax: $" . $event->data->financials->tax . "\n";
    echo "  Total Fee: $" . $event->data->financials->total_fee . "\n";
    echo "  Retainer: $" . $event->data->financials->retainer_fee . "\n";

    // Now add a payment
    $payment = $djep->payments->addToEvent(
        $event->data->eventid,
        500.00,
        'Credit Card',
        ['comments' => 'Deposit payment']
    );
    echo "\nPayment added!\n";
    echo "  Payment ID: " . $payment->data->clpr_idnumber . "\n";
    echo "  Balance Due: $" . $payment->data->balance_due . "\n";

    // Add some music requests
    $djep->musicRequests->add($event->data->eventid, 'Ed Sheeran', 'Perfect', 'MPL');
    $djep->musicRequests->add($event->data->eventid, 'Journey', 'Don\'t Stop Believin\'', 'MPL');
    $djep->musicRequests->add($event->data->eventid, 'Macarena', 'Los del Rio', 'DNP');
    echo "\nMusic requests added!\n";

} catch (ValidationException $e) {
    echo "Validation error: " . $e->getMessage() . "\n";
} catch (DJEPException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
