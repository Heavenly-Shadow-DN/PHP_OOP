<?php
declare(strict_types=1);

namespace Shop\Models;

// Заказ: ссылка на покупателя + итоговая сумма.
// Кладётся в src/Models/Order.php — путь соответствует PSR-4.
class Order
{
    public function __construct(
        public Customer $customer,
        public float $total,
        public string $createdAt = '',
    ) {
        if ($this->createdAt === '') {
            $this->createdAt = date('d.m.Y H:i:s');
        }
    }

    public function info(): string
    {
        return "Заказ для {$this->customer->name} на сумму "
             . number_format($this->total, 2, ',', ' ') . ' ₽ от ' . $this->createdAt;
    }
}
