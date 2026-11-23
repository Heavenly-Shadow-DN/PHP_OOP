<?php
declare(strict_types=1);

class Book
{
    public function __construct(
        public string $title,
        public string $author,
        public int $year,
        public int $pages,
    ) {}

    // строка вида «Мастер и Маргарита — М. Булгаков, 1967, 480 стр.»
    public function getInfo(): string
    {
        return "{$this->title} — {$this->author}, {$this->year}, {$this->pages} стр.";
    }

    // возраст книги в годах
    public function getAge(): int
    {
        return (int)date('Y') - $this->year;
    }
}

class Library
{
    private array $books = [];

    public function addBook(Book $book): void
    {
        $this->books[] = $book;
    }

    // поиск по автору
    public function findByAuthor(string $author): array
    {
        $result = [];
        foreach ($this->books as $b) {
            if ($b->author === $author) {
                $result[] = $b;
            }
        }
        return $result;
    }

    public function count(): int
    {
        return count($this->books);
    }

    public function printAll(): void
    {
        foreach ($this->books as $i => $b) {
            echo ($i + 1) . ". " . $b->getInfo() . PHP_EOL;
        }
    }
}

// === демонстрация ===

$lib = new Library();

$b1 = new Book('Мастер и Маргарита', 'М. Булгаков', 1967, 480);
$b2 = new Book('Собачье сердце',      'М. Булгаков', 1925, 320);
$b3 = new Book('Война и мир',         'Л. Толстой',  1869, 1225);
$b4 = new Book('Анна Каренина',       'Л. Толстой',  1877, 864);
$b5 = new Book('Преступление и наказание', 'Ф. Достоевский', 1866, 671);

$lib->addBook($b1);
$lib->addBook($b2);
$lib->addBook($b3);
$lib->addBook($b4);
$lib->addBook($b5);

echo "=== Все книги ===" . PHP_EOL;
$lib->printAll();

echo PHP_EOL . "=== Поиск: М. Булгаков ===" . PHP_EOL;
foreach ($lib->findByAuthor('М. Булгаков') as $b) {
    echo $b->getInfo() . " (возраст: " . $b->getAge() . " лет)" . PHP_EOL;
}

echo PHP_EOL . "Всего книг: " . $lib->count() . PHP_EOL;

// === разница присваивание / клонирование ===
echo PHP_EOL . "=== Клонирование ===" . PHP_EOL;

$c1 = new Book('Один день Ивана Денисовича', 'А. Солженицын', 1962, 200);
$c2 = $c1;              // ссылка на тот же объект
$c3 = clone $c1;        // независимая копия

$c1->title = 'ИЗМЕНЁННОЕ НАЗВАНИЕ';

echo "c1: " . $c1->getInfo() . PHP_EOL;
echo "c2: " . $c2->getInfo() . PHP_EOL;
echo "c3: " . $c3->getInfo() . PHP_EOL;
