<?php
declare(strict_types=1);

class Student
{
    public const MIN_COURSE = 1;
    public const MAX_COURSE = 4;

    public static int $totalStudents = 0;
    public static string $university = 'Челябинский радиотехнический техникум';

    public function __construct(
        public readonly int $id,
        public string $lastName,
        public string $firstName,
        public int $course,
    ) {
        if ($course < self::MIN_COURSE || $course > self::MAX_COURSE) {
            throw new InvalidArgumentException(
                "Курс должен быть от " . self::MIN_COURSE . " до " . self::MAX_COURSE
            );
        }
        self::$totalStudents++;
    }

    public ?string $email = null;
    public array $grades = [];
    public bool $isActive = true;

    // средний балл
    public function avgGrade(): float
    {
        if (count($this->grades) === 0) {
            return 0.0;
        }
        return array_sum($this->grades) / count($this->grades);
    }
}

// === 4 студента ===
$students = [
    new Student(101, 'Иванов',    'Иван',   1),
    new Student(102, 'Липескина', 'Полина', 2),
    new Student(103, 'Радченко',  'Артём',  3),
    new Student(104, 'Танаева',   'Дарья',  4),
];

$students[0]->email = 'ivan@mail.ru';
$students[0]->grades = [4, 5, 4, 3];

$students[1]->email = 'polina@mail.ru';
$students[1]->grades = [5, 5, 4, 5];

// у третьего почта осталась null
$students[2]->grades = [3, 4, 3, 4];

$students[3]->email = 'daria@mail.ru';
$students[3]->grades = [5, 5, 5, 5];

// === таблица ===
echo "Билет  ФИО                  Курс  Почта            Средний" . PHP_EOL;
echo str_repeat('-', 60) . PHP_EOL;

foreach ($students as $s) {
    printf(
        "%-6d %-20s %-5d %-16s %.2f\n",
        $s->id,
        $s->lastName . ' ' . $s->firstName,
        $s->course,
        $s->email ?? 'не указана',
        $s->avgGrade(),
    );
}

echo PHP_EOL . "Всего студентов: " . Student::$totalStudents . PHP_EOL;
echo "Учебное заведение: " . Student::$university . PHP_EOL;

// === эксперименты ===
echo PHP_EOL . "=== Эксперименты ===" . PHP_EOL;

// 1) попытка изменить readonly $id
try {
    $students[0]->id = 999;
} catch (Error $e) {
    echo "1) " . $e->getMessage() . PHP_EOL;
}

// 2) строка в int $course при strict_types=1
try {
    $students[0]->course = 'три';
} catch (TypeError $e) {
    echo "2) " . $e->getMessage() . PHP_EOL;
}

// 3) курс = 7
try {
    $bad = new Student(200, 'Тестов', 'Тест', 7);
} catch (InvalidArgumentException $e) {
    echo "3) " . $e->getMessage() . PHP_EOL;
}

// 4) изменение статического $university - видно у всех
Student::$university = 'ЧРТ';
echo "4) После смены university значение через класс: " . Student::$university . PHP_EOL;
echo "   Оно же доступно всем объектам через self:: внутри методов." . PHP_EOL;

// 5) var_dump и get_object_vars
echo PHP_EOL . "5) var_dump одного студента:" . PHP_EOL;
var_dump($students[0]);

echo PHP_EOL . "get_object_vars:" . PHP_EOL;
print_r(get_object_vars($students[0]));
