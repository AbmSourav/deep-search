<?php

/**
 * Mock WP_Query class for testing
 */
if (! class_exists('WP_Query')) {
    class WP_Query
    {
        public int $found_posts = 0;
        public int $max_num_pages = 0;
        public array $posts = [];
        private int $currentIndex = -1;

        public function __construct(public array $args = [])
        {
        }

        public function have_posts(): bool
        {
            return $this->currentIndex < count($this->posts) - 1;
        }

        public function the_post(): void
        {
            $this->currentIndex++;
        }

        public function get_posts(): array
        {
            return $this->posts;
        }
    }
}
