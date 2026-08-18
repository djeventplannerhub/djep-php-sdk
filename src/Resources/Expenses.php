<?php
namespace DJEP\Resources;

class Expenses extends Resource
{
    public function list(array $params = []): object
    {
        return $this->http->get('expenses', $params);
    }

    public function all(array $params = []): \Generator
    {
        return $this->autoPaginate('expenses', $params);
    }

    public function categories(): object
    {
        return $this->http->get('expense_categories');
    }

    public function payees(): object
    {
        return $this->http->get('expense_payees');
    }

    public function paymentMethods(): object
    {
        return $this->http->get('expense_payment_methods');
    }
}
