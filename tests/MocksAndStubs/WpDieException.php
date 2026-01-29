<?php

namespace DeepSearch\Tests\MocksAndStubs;

/**
 * Exception to simulate wp_send_json_* functions which call die()
 */
class WpDieException extends \Exception
{
    public string $response;
    public int $statusCode;

    public function __construct(string $response, int $statusCode)
    {
        $this->response = $response;
        $this->statusCode = $statusCode;
        parent::__construct('WordPress die() called');
    }

    public function getResponseData(): array
    {
        return json_decode($this->response, true);
    }
}
