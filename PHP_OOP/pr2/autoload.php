<?php
declare(strict_types=1);

// автозагрузчик: префикс Shop\ -> папка src/
spl_autoload_register(function (string $class): void {
    $prefix = 'Shop\\';
    $baseDir = __DIR__ . '/src/';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));       // Models\Product
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        echo "[autoload] Загружен класс: $class" . PHP_EOL;
        require $file;
    }
});
