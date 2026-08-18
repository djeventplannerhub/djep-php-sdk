<?php
/**
 * DJEP PHP SDK - Basic Usage Example
 *
 * This example demonstrates common API operations.
 */

require __DIR__ . '/../autoload.php';

use DJEP\Client;
use DJEP\Exceptions\DJEPException;

// Initialize the client (use environment variables for your API key)
$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'));

try {
    // Check API status
    $status = $djep->status();
    echo "API Version: " . $status->data->api_version . "\n";
    echo "Endpoints available: " . count($status->data->endpoints) . "\n\n";

    // List first 5 events
    $events = $djep->events->list(['per_page' => 5, 'sort_by' => 'event_date', 'sort_dir' => 'DESC']);
    echo "Total events: " . $events->pagination->total_records . "\n";
    foreach ($events->data as $event) {
        echo "  [{$event->status}] {$event->event_type} on {$event->event_date}\n";
    }

    // List clients
    echo "\nClients:\n";
    $clients = $djep->clients->list(['per_page' => 5]);
    foreach ($clients->data as $client) {
        echo "  {$client->first_name} {$client->last_name} ({$client->email})\n";
    }

    // Check availability for a date
    echo "\nAvailability check:\n";
    $avail = $djep->availability->check('12/15/2026');
    echo "  Date: 12/15/2026\n";
    echo "  Available: " . ($avail->data->available ? 'Yes' : 'No') . "\n";

} catch (DJEPException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
