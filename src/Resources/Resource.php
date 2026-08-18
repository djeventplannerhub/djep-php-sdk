<?php
namespace DJEP\Resources;

use DJEP\HttpClient;

abstract class Resource
{
    protected HttpClient $http;

    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Auto-paginate through all records
     * Yields each record one at a time, fetching next pages automatically
     *
     * @param string $action   The API action name
     * @param array  $params   Query parameters (per_page will be set to 100 for efficiency)
     * @return \Generator
     */
    protected function autoPaginate(string $action, array $params = []): \Generator
    {
        $params['per_page'] = $params['per_page'] ?? 100;
        $params['page'] = 1;

        do {
            $response = $this->http->get($action, $params);
            $data = $response->data ?? [];

            foreach ($data as $record) {
                yield $record;
            }

            $pagination = $response->pagination ?? null;
            $hasMore = $pagination && $params['page'] < $pagination->total_pages;
            $params['page']++;
        } while ($hasMore);
    }
}
