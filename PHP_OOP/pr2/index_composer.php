<?php
declare(strict_types=1);

// Вариант для Composer.
// Перед запуском:
//   1) composer dump-autoload
//   2) в этом файле autoload.php больше не подключается — вместо него vendor/autoload.php
// Отладочный вывод "[autoload] Загружен класс: …" в Composer-версии отсутствует.

require __DIR__ . '/vendor/autoload.php';

use Shop\Models\Customer;
use Shop\Models\Order;
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

// Класс Shop\Models\Order теперь существует и грузится автоматически.
// С Composer'ом его не нужно регистрировать отдельно —
// PSR-4 сам найдёт src/Models/Order.php по имени класса.
$order = new Order($customer, $cart->getTotal());
echo $order->info() . PHP_EOL;
