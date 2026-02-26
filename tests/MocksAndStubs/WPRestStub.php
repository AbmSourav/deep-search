<?php

/**
 * Mock WP_REST_Request class for testing
 */
if (! class_exists('WP_REST_Request')) {
    class WP_REST_Request
    {
        private array $params = [];

        public function __construct(string $method = 'GET', string $route = '')
        {
        }

        public function set_param(string $key, $value): void
        {
            $this->params[$key] = $value;
        }

        public function get_param(string $key)
        {
            return $this->params[$key] ?? null;
        }

        public function get_params(): array
        {
            return $this->params;
        }
    }
}

/**
 * Mock WP_REST_Response class for testing
 */
if (! class_exists('WP_REST_Response')) {
    class WP_REST_Response
    {
        public $data;
        public int $status;

        public function __construct($data = null, int $status = 200)
        {
            $this->data = $data;
            $this->status = $status;
        }

        public function get_data()
        {
            return $this->data;
        }

        public function get_status(): int
        {
            return $this->status;
        }
    }
}
