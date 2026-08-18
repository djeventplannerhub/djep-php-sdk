<?php
namespace DJEP;

use DJEP\Exceptions\DJEPException;
use DJEP\Exceptions\AuthenticationException;
use DJEP\Exceptions\NotFoundException;
use DJEP\Exceptions\ValidationException;
use DJEP\Exceptions\RateLimitException;
use DJEP\Exceptions\ForbiddenException;

class HttpClient
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout;
    private bool $sslVerify;

    public function __construct(string $apiKey, string $domain, array $options = [])
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = rtrim($options['base_url'] ?? "https://{$domain}/api/api.asp", '/');
        $this->timeout = $options['timeout'] ?? 30;
        $this->sslVerify = $options['ssl_verify'] ?? true;
    }

    /**
     * Make a GET request to the API
     */
    public function get(string $action, array $params = []): object
    {
        $params['action'] = $action;
        $url = $this->baseUrl . '?' . http_build_query($params);
        return $this->request('GET', $url);
    }

    /**
     * Make a POST request to the API
     */
    public function post(string $action, array $data = []): object
    {
        $url = $this->baseUrl . '?action=' . urlencode($action);
        return $this->request('POST', $url, $data);
    }

    /**
     * Execute the cURL request
     */
    private function request(string $method, string $url, array $postData = []): object
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Accept: application/json',
            ],
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }

        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        curl_close($ch);

        // cURL-level errors (network, timeout, SSL)
        if ($curlErrno !== 0) {
            throw new DJEPException("cURL error ({$curlErrno}): {$curlError}", $curlErrno);
        }

        // Parse response
        $response = json_decode($responseBody);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new DJEPException(
                "Invalid JSON response from API (HTTP {$httpCode}): " . substr($responseBody, 0, 200),
                $httpCode
            );
        }

        // Handle error responses
        if ($httpCode >= 400 || (isset($response->success) && $response->success === false)) {
            $message = $response->error->message ?? $response->message ?? 'Unknown API error';
            $code = $response->error->code ?? $httpCode;

            switch ($httpCode) {
                case 401:
                    throw new AuthenticationException($message, $code);
                case 403:
                    throw new ForbiddenException($message, $code);
                case 404:
                    throw new NotFoundException($message, $code);
                case 429:
                    throw new RateLimitException($message, $code);
                case 400:
                    throw new ValidationException($message, $code);
                default:
                    throw new DJEPException($message, $code);
            }
        }

        return $response;
    }
}
