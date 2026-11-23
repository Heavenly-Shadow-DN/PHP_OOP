<?php
declare(strict_types=1);

class Employee
{
    public string $name;
    protected float $baseSalary;
    private string $passport;
    private array $salaryHistory = [];

    public function __construct(string $name, float $baseSalary, string $passport)
    {
        $this->name = $name;
        $this->baseSalary = $baseSalary;
        $this->passport = $passport;
    }

    public function getSalary(): float
    {
        return $this->baseSalary;
    }

    // повышение оклада от 1 до 50 %
    public function raiseSalary(float $percent): void
    {
        $this->validatePercent($percent);
        $this->baseSalary *= (1 + $percent / 100);
        $this->salaryHistory[] = date('d.m.Y') . ": +$percent% -> {$this->baseSalary}";
    }

    // маска паспорта: **** **3456
    final public function getMaskedPassport(): string
    {
        $last = substr($this->passport, -4);
        return "**** **$last";
    }

    public function getSalaryHistory(): array
    {
        return $this->salaryHistory;
    }

    // налог 13%
    protected function calculateTax(float $amount): float
    {
        return $amount * 0.13;
    }

    // проверка процента
    private function validatePercent(float $p): void
    {
        if ($p < 1 || $p > 50) {
            throw new InvalidArgumentException("Процент должен быть от 1 до 50");
        }
    }
}

class Manager extends Employee
{
    private int $teamSize;

    public function __construct(string $name, float $baseSalary, string $passport, int $teamSize)
    {
        parent::__construct($name, $baseSalary, $passport);
        $this->teamSize = $teamSize;
    }

    public function getSalary(): float
    {
        // оклад + 5000 за каждого подчинённого
        return $this->baseSalary + 5000 * $this->teamSize;
    }

    public function getNetSalary(): float
    {
        $gross = $this->getSalary();
        return $gross - $this->calculateTax($gross);
    }
}

class Developer extends Employee
{
    private string $level;
    private const COEFFS = ['junior' => 1.0, 'middle' => 1.5, 'senior' => 2.0];

    public function __construct(string $name, float $baseSalary, string $passport, string $level)
    {
        parent::__construct($name, $baseSalary, $passport);
        $this->level = $level;
    }

    public function getSalary(): float
    {
        $coef = self::COEFFS[$this->level] ?? 1.0;
        return $this->baseSalary * $coef;
    }

    public function getNetSalary(): float
    {
        $gross = $this->getSalary();
        return $gross - $this->calculateTax($gross);
    }

    // попытка переопределить final-метод — раскомментируйте, чтобы увидеть ошибку:
    // public function getMaskedPassport(): string { return 'nope'; }
}

// === эксперименты (таблица) ===
echo "=== Таблица экспериментов ===" . PHP_EOL;

$emp = new Manager('Иван', 80000, '1234567890', 5);

// 1) чтение $name снаружи
echo "1) \$name снаружи: " . $emp->name . PHP_EOL;

// 2) чтение $baseSalary снаружи
// echo $emp->baseSalary; // Error: Cannot access protected property
echo "2) \$baseSalary снаружи: Cannot access protected property" . PHP_EOL;

// 3) чтение $baseSalary в Manager — protected, доступно наследнику
echo "3) \$baseSalary в Manager: доступен через \$this->baseSalary (используется в getSalary())" . PHP_EOL;

// 4) чтение $passport в Manager
// echo $this->passport; // в коде Manager нельзя — private
echo "4) \$passport в Manager: Cannot access private property" . PHP_EOL;

// 5) вызов calculateTax() снаружи
// $emp->calculateTax(1000); // Error: Call to protected method
echo "5) calculateTax() снаружи: Call to protected method" . PHP_EOL;

// 6) вызов validatePercent() в Developer
// $this->validatePercent(10); // в Developer нельзя — private родителя
echo "6) validatePercent() в Developer: Call to private method" . PHP_EOL;

// === попытка переопределить final-метод ===
echo PHP_EOL . "=== final-метод ===" . PHP_EOL;
echo "Если раскомментировать getMaskedPassport() в Developer, будет ошибка:" . PHP_EOL;
echo "Cannot override final method Employee::getMaskedPassport()" . PHP_EOL;

// === массив сотрудников ===
echo PHP_EOL . "=== Сотрудники ===" . PHP_EOL;

$staff = [
    new Manager('Полина',   90000, '4510223344', 4),
    new Developer('Сергей',  70000, '7788990011', 'middle'),
    new Developer('Дарья', 60000, '1122334455', 'junior'),
    new Developer('Артём', 120000, '5566778899', 'senior'),
];

foreach ($staff as $e) {
    $gross = $e->getSalary();
    if ($e instanceof Manager) {
        $net = $e->getNetSalary();
    } else {
        $net = $e->getNetSalary();
    }
    printf(
        "%-10s %-10s  gross: %.0f  net: %.0f  passport: %s\n",
        $e->name,
        $e::class,
        $gross,
        $net,
        $e->getMaskedPassport(),
    );
}

// повышение оклада
echo PHP_EOL . "=== Повышение оклада ===" . PHP_EOL;
$emp->raiseSalary(15);
echo "Новый оклад: " . $emp->getSalary() . PHP_EOL;
echo "История: " . PHP_EOL;
print_r($emp->getSalaryHistory());
