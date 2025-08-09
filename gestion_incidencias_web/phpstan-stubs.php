<?php
    declare(strict_types=1);
    /**
     * @param string               $view
     * @param array<string,mixed> $data
     */
    function authView(string $view, array $data = []): void {}

    /**
     * @param string $route
     * @return string
     */
    function url(string $route): string { return ''; }

    /**
     * @param string               $view
     * @param array<string,mixed> $data
     */
    function view(string $view, array $data = []): void {}

    /**
     * @param string                 $endpoint
     * @param string                 $method
     * @param array<string,mixed>    $payload
     * @return array<int|string,mixed>
     */
    function apiRequest(string $endpoint, string $method = 'GET', array $payload = []): array {
        return [];
    }

    /** @var string */
    const API_BASE = '';
    /** @var string */
    const ADMIN_BASE = '';
?>