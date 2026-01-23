<?php

// @codingStandardsIgnoreFile
// This file is never executed - only provides IDE autocompletion for Pest

namespace {
    exit('This file should not be included.');

    /**
     * @template TValue
     * @param TValue $value
     * @return \Pest\Expectation<TValue>
     */
    function expect(mixed $value): \Pest\Expectation {}

    /**
     * @return \Pest\PendingCalls\TestCall
     */
    function it(string $description, ?\Closure $closure = null): \Pest\PendingCalls\TestCall {}

    /**
     * @return \Pest\PendingCalls\TestCall
     */
    function test(string $description, ?\Closure $closure = null): \Pest\PendingCalls\TestCall {}

    /**
     * @return \Pest\PendingCalls\BeforeEachCall
     */
    function beforeEach(?\Closure $closure = null): \Pest\PendingCalls\BeforeEachCall {}

    function afterEach(?\Closure $closure = null): void {}

    function beforeAll(?\Closure $closure = null): void {}

    function afterAll(?\Closure $closure = null): void {}
}
