<?php

    function view(string $view, array $data = [])
    {
        extract($data);
        ob_start();
        require __DIR__ . "/views/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/views/layout.php";
    }

    function url(string $path): string
    {
        return BASE_URL . $path;
    }

    function authView(string $view, array $data = [])
    {
        extract($data);
        ob_start();
        require __DIR__ . "/views/auth/{$view}.php";
        $content = ob_get_clean();
        require __DIR__ . "/views/auth/layout.php";
    }

?>