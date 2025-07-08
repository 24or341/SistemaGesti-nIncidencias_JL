<?php
    declare(strict_types=1);

    /**
     *
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
     * @param string $endpoint
     * @param string $method
     * @return array<string,mixed>
     */
    function apiRequest(string $endpoint, string $method): array {
        return [];
    }

    /** @var string */
    const API_BASE = '';
    /** @var string */
    const ADMIN_BASE = '';
?>