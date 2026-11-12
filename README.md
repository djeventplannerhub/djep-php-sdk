# DJEP PHP SDK

Official PHP SDK for the [DJ Event Planner (DJEP)](https://www.djeventplanner.com) REST API.

## Requirements

- PHP 7.4 or later
- cURL extension
- JSON extension

## Installation

### Via Composer (recommended)

```bash
composer require djeventplannerhub/djep-php-sdk
```

### Manual Installation

Download the SDK and include the autoloader:

```php
require 'djep-php-sdk/autoload.php';
```

## Quick Start

```php
use DJEP\Client;

// Load your API key from environment variables (never hardcode keys)
$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'));

// Check API status
$status = $djep->status();
echo $status->data->api_version;

// List upcoming events
$events = $djep->events->list(['status' => 'Booked', 'per_page' => 10]);
foreach ($events->data as $event) {
    echo $event->event_type . ' on ' . $event->event_date . "\n";
}
```

## Authentication

Your API key is generated in DJEP under **Setup > Integrations > API Key**. The key format depends on your role:

- **Master Admin:** `secretkey_djidnumber`
- **Employee/Salesperson:** `secretkey_djidnumber_employeeid`

**Important:** Never hardcode your API key in source files. Use environment variables or a config file outside your web root.

```php
// Option 1: Environment variable (recommended)
$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'));

// Option 2: Config file outside web root
$config = require '/path/outside/webroot/djep-config.php';
$djep = new Client($config['api_key'], $config['domain']);

// Option 3: WordPress (wp-config.php constants)
$djep = new Client(DJEP_API_KEY, DJEP_DOMAIN);
```

## Usage

### Events

```php
// List events
$events = $djep->events->list(['status' => 'Booked', 'sort_by' => 'event_date']);

// Get a single event
$event = $djep->events->get(56789);

// Create an event (financials auto-calculated from package)
$event = $djep->events->create([
    'clientid' => 12345,
    'event_date' => '2026-09-15',
    'event_type' => 'Wedding',
    'start_time' => '3:00 PM',
    'end_time' => '11:00 PM',
    'addons' => '501:2,502:1',
]);
echo "Total fee: " . $event->data->financials->total_fee;

// Update specific fields
$djep->events->update(56789, [
    'status' => 'Confirmed',
    'guest_count' => 200,
    'comments' => 'Client confirmed by phone',
]);

// Update and recalculate financials
$djep->events->update(56789, ['pkg_idnumber' => 14200], true);

// Delete an event (cascading)
$djep->events->delete(56789);

// Event sub-data
$payments = $djep->events->payments(56789);
$songs = $djep->events->musicRequests(56789);
$planning = $djep->events->planning(56789);
```

### Clients

```php
$clients = $djep->clients->list(['per_page' => 25]);
$client = $djep->clients->get(12345);

$newClient = $djep->clients->create([
    'first_name' => 'John',
    'last_name' => 'Smith',
    'email' => 'john@example.com',
]);

$djep->clients->update(12345, ['email' => 'new@example.com']);
$djep->clients->delete(12345);
```

### Venues, Employees, Packages, Addons, Contacts

```php
// All follow the same pattern
$venues = $djep->venues->list();
$venue = $djep->venues->get(789);

$employees = $djep->employees->list();
$packages = $djep->packages->list();
$addons = $djep->addons->list();
$contacts = $djep->contacts->list();
```

### Contacts

```php
$contacts = $djep->contacts->list();
$contact = $djep->contacts->get(1016);

// Create a contact. At least one of first_name, last_name or company.
$djep->contacts->create([
    'first_name'     => 'Ada',
    'last_name'      => 'Lovelace',
    'company'        => 'Analytical Engines',
    'email_address'  => 'ada@example.com',
    'business_phone' => '555 0100',
]);

// Update one. Only the fields you pass are written.
$djep->contacts->update(1016, ['job_title' => 'Head of Catering']);
```

Creating and editing contacts is for administrators, master admins and
salespeople. An employee with "Add Vendors To Events" can attach an existing
vendor to an event, but cannot create the record itself.

### Vendors

```php
$vendors = $djep->vendors->list();
$vendor = $djep->vendors->get(3456);

// Create a vendor. Same fields as a contact, marked as a vendor for you.
$djep->vendors->create(['company' => 'Big Lights Co', 'business_phone' => '555 0199']);

// Update one
$djep->vendors->update(3456, ['notes' => 'Preferred supplier']);

// Link a vendor to an event
$djep->vendors->linkToEvent(56789, 3456);
```

`vendors->update()` only reaches contacts already marked as vendors. A general
contact returns `404` here even though it exists, so use `contacts->update()`
for those.

### Payments

```php
// List all payments company-wide
$payments = $djep->payments->list(['per_page' => 50]);

// Add a payment to an event
$result = $djep->payments->addToEvent(56789, 500.00, 'Credit Card', [
    'processing_fee' => 15.00,
    'comments' => 'Final payment',
]);
echo "Balance due: " . $result->data->balance_due;
```

### Music Requests

```php
// Get requests for an event
$songs = $djep->musicRequests->forEvent(56789);

// Add a music request
$djep->musicRequests->add(56789, 'Queen', 'Bohemian Rhapsody', 'MPL');
$djep->musicRequests->add(56789, 'ABBA', 'Dancing Queen', 'DED', 'For the bride');

// Update one. Only the fields you pass are written.
$djep->musicRequests->update(88, ['type' => 'DNP', 'comments' => 'Bride asked us to drop this']);

// Remove one. This cannot be undone.
$djep->musicRequests->delete(88);
```

### Timelines

```php
// Replace the running order. Send every activity you want kept.
$djep->planning->updateTimeline(56789, [
    ['time' => '6:00 PM', 'name' => 'Guests arrive'],
    ['time' => '7:00 PM', 'name' => 'Speeches',    'comments' => 'Three, ten minutes each'],
    ['time' => '7:30 PM', 'name' => 'First dance', 'comments' => 'Dim the lights'],
], 'Reception running order');

// Second timeline
$djep->planning->updateTimeline(56789, $activities, 'Ceremony', 2);

// Clear a timeline
$djep->planning->updateTimeline(56789, []);
```

Up to 52 activities are stored. This replaces the whole timeline rather than
appending, and clearing it removes the headline too.

### Check In And Check Out

```php
// Check yourself in, with the location
$djep->events->checkIn(56789, 0, ['latitude' => '50.7989', 'longitude' => '-1.0912']);

// An administrator checking somebody else in
$djep->events->checkIn(56789, 19842);

$djep->events->checkOut(56789);
```

One way only, the same as the buttons in the web app. A second check in returns
`409`, and checking out before checking in returns `400`.

Read `notification_status` on the response rather than `notification_sent`: the
office email can be switched off, have no recipient, or be blocked by the
server, and the status says which.

### Event Confirmations

```php
// An employee confirming their own assignment (recorded as C)
$djep->events->confirmAssignment(56789);

// An administrator confirming for somebody (recorded as A)
$djep->events->confirmAssignment(56789, 19842);

// A reply collected earlier and sent on later
$djep->events->confirmAssignment(56789, 0, '2026-06-13 14:02');

// Declining, which only the employee themselves can do
$result = $djep->events->declineAssignment(56789, 'Double booked');
echo $result->data->reassign_to_employee;  // standby employee, or null
```

An assignment can only be answered once; a second answer returns `409`. Where
the account nominates a standby employee a decline reports them, but nothing is
reassigned for you.

### Booking Helpers

```php
// List available helpers
$helpers = $djep->bookingHelpers->list();

// Run a helper by unique_id (recommended)
$result = $djep->bookingHelpers->run(56789, 'book_event_1');
echo $result->data->log;

// Or by position index
$result = $djep->bookingHelpers->run(56789, null, 0);
```

### Availability

```php
// Check a single date
$avail = $djep->availability->check('09/15/2026');
if ($avail->data->available) {
    echo $avail->data->employees->available . ' employees available';
}

// Check a date range (max 90 days)
$range = $djep->availability->range('09/01/2026', '09/30/2026');
foreach ($range->data->dates as $date) {
    echo $date->date . ': ' . ($date->available ? 'Available' : 'Unavailable') . "\n";
}
```

### Settings

```php
// Website tools (form config, field labels, validation)
$rfiSettings = $djep->settings->websiteTools('request_info');

// Company settings
$company = $djep->settings->company();

// Custom field configuration
$fields = $djep->settings->customFields();
```

### Expenses

```php
$expenses = $djep->expenses->list(['per_page' => 25]);
$categories = $djep->expenses->categories();
$payees = $djep->expenses->payees();
$methods = $djep->expenses->paymentMethods();
```

### Other Resources

```php
// Submissions (read)
$rfi = $djep->submissions->rfi();
$quotes = $djep->submissions->quotes();
$contactUs = $djep->submissions->contactUs();

// Closed dates, equipment, systems
$closedDates = $djep->closedDates->list();
$equipment = $djep->equipment->list();
$systems = $djep->systems->list();
```

### Create Submission (RFI)

Push leads from external sources into the DJ's submissions queue:

```php
// Wedding Wire lead
$submission = $djep->submissions->create([
    'first_name' => 'Sarah',
    'last_name' => 'Johnson',
    'email' => 'sarah@example.com',
    'telephone' => '555-0123',
    'event_date' => '2026-09-15',
    'event_type' => 'Wedding',
    'guest_count' => 200,
    'source' => 'Wedding Wire',
    'send_notification' => 'true',
]);
echo "Submission ID: " . $submission->data->req_idnumber;

// With custom questions and privacy consent
$submission = $djep->submissions->create([
    'first_name' => 'Emma',
    'last_name' => 'Wilson',
    'email' => 'emma@example.com',
    'event_date' => '2027-06-20',
    'event_type' => 'Wedding',
    'question_1' => 'Yes, we need lighting',
    'q1_mapto' => 'custom_field1',
    'privacy_policy_signed' => 'true',
    'source' => 'Custom Website',
    'send_notification' => 'true',
]);
```

### Convenience Endpoints

Quick actions without the `fields` parameter:

```php
// Quick status change — returns old and new status
$result = $djep->events->updateStatus(56789, 'Booked');
echo $result->data->old_status . ' → ' . $result->data->new_status;

// Assign employee to position (1-15), with optional fee and role
$djep->events->assignEmployee(56789, 1, 48113, ['fee' => 500, 'role' => 'Lead DJ']);
$djep->events->assignEmployee(56789, 2, 48361, ['fee' => 350, 'role' => 'MC']);

// Unassign position 3
$djep->events->assignEmployee(56789, 3, 0);

// Update notes — pass any combination of note fields
$djep->events->updateNotes(56789, [
    'comments' => 'AI Summary: Client wants 80s and 90s hits',
    'next_action' => 'Send playlist proposal',
    'next_action_date' => '08/15/2026',
]);
```

## Auto-Pagination

For resources with pagination, use `all()` to automatically iterate through every page:

```php
// Fetches all events across all pages
foreach ($djep->events->all(['status' => 'Booked']) as $event) {
    echo $event->event_date . "\n";
}

// Works with any paginated resource
foreach ($djep->clients->all() as $client) {
    echo $client->first_name . ' ' . $client->last_name . "\n";
}
```

## Error Handling

The SDK throws specific exceptions for different error types:

```php
use DJEP\Exceptions\AuthenticationException;
use DJEP\Exceptions\NotFoundException;
use DJEP\Exceptions\ValidationException;
use DJEP\Exceptions\ForbiddenException;
use DJEP\Exceptions\RateLimitException;
use DJEP\Exceptions\DJEPException;

try {
    $event = $djep->events->get(99999);
} catch (AuthenticationException $e) {
    // Invalid or missing API key (401)
    echo "Auth error: " . $e->getMessage();
} catch (NotFoundException $e) {
    // Resource not found (404)
    echo "Not found: " . $e->getMessage();
} catch (ForbiddenException $e) {
    // Insufficient permissions (403)
    echo "Forbidden: " . $e->getMessage();
} catch (ValidationException $e) {
    // Invalid parameters (400)
    echo "Validation error: " . $e->getMessage();
} catch (RateLimitException $e) {
    // Too many requests (429)
    echo "Rate limited: " . $e->getMessage();
} catch (DJEPException $e) {
    // Any other API error
    echo "Error: " . $e->getMessage();
}
```

## Configuration Options

```php
$djep = new Client(getenv('DJEP_API_KEY'), getenv('DJEP_DOMAIN'), [
    'timeout' => 60,        // Request timeout in seconds (default: 30)
    'ssl_verify' => false,   // Disable SSL verification (default: true)
]);
```

## Security

- **Never** commit API keys to version control
- Store keys in environment variables, `.env` files (excluded from Git), or config files outside your web root
- Use the most restrictive API key for your use case (employee key instead of master admin where possible)
- API keys provide full access to the associated account — treat them like passwords

## API Documentation

Full API documentation is available at your DJEP instance:

```
https://yourdomain.com/api/api.asp?action=docs
```

## License

MIT
