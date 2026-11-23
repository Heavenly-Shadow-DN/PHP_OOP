<?php
declare(strict_types=1);

require __DIR__ . '/autoload.php';

use Shop\Models\Customer;
use Shop\Models\Product;
use Shop\Services\Cart;
use Shop\Helpers\PriceFormatter;

$customer = new Customer('Иван', 'ivan@mail.ru');

$cart = new Cart($customer);
$cart->add(new Product('Клавиатура', 2500), 1);
$cart->add(new Product('Мышь',      1200), 2);
$cart->add(new Product('Коврик',     400), 3);

echo "Покупатель: " . $customer->name . PHP_EOL;
echo "Итого: " . PriceFormatter::format($cart->getTotal()) . PHP_EOL;

// Дополнительно: класс Shop\Models\Order загружается автоматически
// через тот же автозагрузчик, без отдельных require.
$order = new Shop\Models\Order($customer, $cart->getTotal());
echo $order->info() . PHP_EOL;

// проверка ошибки: несуществующий класс
echo PHP_EOL . "=== Обращение к несуществующему классу ===" . PHP_EOL;
try {
    $order = new Shop\Models\Invoice();
} catch (Error $e) {
    echo "Ошибка: " . $e->getMessage() . PHP_EOL;
}
