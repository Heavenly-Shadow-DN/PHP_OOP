<?php
declare(strict_types=1);

namespace Shop\Services;

use Shop\Models\Product;
use Shop\Models\Customer;

class Cart
{
    private array $items = [];

    public function __construct(
        public Customer $customer,
    ) {}

    public function add(Product $p, int $qty): void
    {
        $this->items[] = ['product' => $p, 'qty' => $qty];
    }

    public function getTotal(): float
    {
        $sum = 0.0;
        foreach ($this->items as $item) {
            $sum += $item['product']->price * $item['qty'];
        }
        return $sum;
    }
}
