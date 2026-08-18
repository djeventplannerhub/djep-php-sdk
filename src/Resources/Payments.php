<?php
namespace DJEP\Resources;

class Payments extends Resource
{
    /**
     * List all payments company-wide (paginated)
     */
    public function list(array $params = []): object
    {
        return $this->http->get('payments', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('payments', $params);
    }

    /**
     * Add a payment to an event
     *
     * @param int    $eventid  The event to add payment to
     * @param float  $amount   Payment amount
     * @param string $method   Payment method (e.g. "Cash", "Check", "Credit Card")
     * @param array  $options  Optional: date, processing_fee, comments, notes, deposited, etc.
     */
    public function addToEvent(int $eventid, float $amount, string $method, array $options = []): object
    {
        $data = array_merge($options, [
            'eventid' => $eventid,
            'amount' => $amount,
            'method' => $method,
        ]);
        return $this->http->post('add_payment', $data);
    }
}
