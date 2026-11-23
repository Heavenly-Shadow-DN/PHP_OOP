<?php
declare(strict_types=1);

class QueryBuilder
{
    private string $table;
    private array $columns = [];
    private array $wheres = [];
    private string $orderColumn = '';
    private string $orderDir = 'ASC';
    private ?int $limitValue = null;

    // список разрешённых операторов
    private const OPERATORS = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];

    private function __construct(string $table)
    {
        $this->table = $table;
    }

    // статическая фабрика
    public static function table(string $name): static
    {
        return new static($name);
    }

    public function select(string ...$columns): static
    {
        $this->columns = $columns;
        return $this;
    }

    public function where(string $column, string $operator, string|int $value): static
    {
        if (!in_array($operator, self::OPERATORS, true)) {
            throw new InvalidArgumentException("Недопустимый оператор: $operator");
        }
        $this->wheres[] = "$column $operator " . $this->quote($value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): static
    {
        $direction = strtoupper($direction);
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            throw new InvalidArgumentException("Направление только ASC или DESC");
        }
        $this->orderColumn = $column;
        $this->orderDir = $direction;
        return $this;
    }

    public function limit(int $count): static
    {
        if ($count <= 0) {
            throw new InvalidArgumentException("limit должен быть больше 0");
        }
        $this->limitValue = $count;
        return $this;
    }

    // собрать итоговый SQL
    public function toSql(): string
    {
        $cols = empty($this->columns) ? '*' : implode(', ', $this->columns);
        $sql = "SELECT $cols FROM {$this->table}";

        if (!empty($this->wheres)) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        if ($this->orderColumn !== '') {
            $sql .= " ORDER BY {$this->orderColumn} {$this->orderDir}";
        }
        if ($this->limitValue !== null) {
            $sql .= " LIMIT {$this->limitValue}";
        }
        return $sql;
    }

    // строки в кавычки, числа как есть
    private function quote(string|int $value): string
    {
        if (is_int($value)) {
            return (string)$value;
        }
        return "'" . $value . "'";
    }

    public function __toString(): string
    {
        return $this->toSql();
    }
}

// === демонстрация ===

echo "=== Запрос 1 ===" . PHP_EOL;
$q1 = QueryBuilder::table('users')
    ->select('id', 'name', 'email')
    ->where('age', '>=', 18)
    ->where('city', '=', 'Москва')
    ->orderBy('name')
    ->limit(10);
echo $q1 . PHP_EOL;

echo PHP_EOL . "=== Запрос 2 (без select, без limit) ===" . PHP_EOL;
$q2 = QueryBuilder::table('orders')
    ->where('status', '=', 'paid')
    ->orderBy('created_at', 'DESC');
echo $q2 . PHP_EOL;

echo PHP_EOL . "=== Запрос 3 (LIKE) ===" . PHP_EOL;
$q3 = QueryBuilder::table('products')
    ->select('id', 'title')
    ->where('title', 'LIKE', '%телефон%')
    ->limit(5);
echo $q3 . PHP_EOL;

// === перехват ошибок ===
echo PHP_EOL . "=== Ошибки ===" . PHP_EOL;

try {
    QueryBuilder::table('t')->where('a', '<>', 1);
} catch (InvalidArgumentException $e) {
    echo "Оператор: " . $e->getMessage() . PHP_EOL;
}

try {
    QueryBuilder::table('t')->orderBy('a', 'RANDOM');
} catch (InvalidArgumentException $e) {
    echo "Сортировка: " . $e->getMessage() . PHP_EOL;
}

try {
    QueryBuilder::table('t')->limit(0);
} catch (InvalidArgumentException $e) {
    echo "Limit: " . $e->getMessage() . PHP_EOL;
}
