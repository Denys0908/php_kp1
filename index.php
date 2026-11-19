<?php
// Крок 7 методички: вмикаємо відображення всіх помилок PHP на час розробки
error_reporting(E_ALL);
ini_set('display_errors', '1');

$projectTitle = 'КП-1: Система обліку студентів та оцінок (Варіант 11)';

// Крок 2 методички: масив асоціативних масивів (5 записів, поля: name, group, avgGrade)
// Усі вхідні дані винесено в один блок — при їх зміні таблиця й підсумки перерахуються автоматично
$students = [
    ['name' => 'Петренко Петро', 'group' => 'ІК-44', 'avgGrade' => 94.5],
    ['name' => 'Мельник Анна', 'group' => 'ІК-44', 'avgGrade' => 86.0],
    ['name' => 'Бондаренко Максим', 'group' => 'ІК-44', 'avgGrade' => 90.0],
    ['name' => 'Ткаченко Софія', 'group' => 'ІК-44', 'avgGrade' => 72.5],
    ['name' => 'Шевчук Дмитро', 'group' => 'ІК-44', 'avgGrade' => 58.0],
];

// Крок 3 методички: типізована функція форматування одного запису студента
function formatStudent(array $student): string
{
    return "{$student['name']} (група {$student['group']})";
}

// Крок 4 методички: умовна логіка (if/elseif/else) для обчислення статусу
// За умовою варіанта 11 обов'язковий поріг >= 90 («Відмінник»);
// пороги 75 та 60 додано додатково, за власним вибором
function getStudentStatus(float $avgGrade): string
{
    if ($avgGrade >= 90) {
        return 'Відмінник';
    } elseif ($avgGrade >= 75) {
        return 'Добре';
    } elseif ($avgGrade >= 60) {
        return 'Задовільно';
    } else {
        return 'Незадовільно';
    }
}

// Крок 6 методички: динамічне обчислення агрегатного показника (середній бал по групі)
function calculateAverageGrade(array $students): float
{
    $totalCount = count($students);
    if ($totalCount === 0) {
        return 0.0;
    }

    $grades = array_column($students, 'avgGrade');
    return array_sum($grades) / $totalCount;
}

// Підготовка підсумкових показників для виводу на сторінку
$totalStudents = count($students);
$averageGrade = calculateAverageGrade($students);

$excellentStudents = array_filter($students, function (array $student): bool {
    return getStudentStatus($student['avgGrade']) === 'Відмінник';
});
$excellentCount = count($excellentStudents);

// Динамічно визначаємо назву групи (або перелік груп) з наявного масиву
$groupsList = array_unique(array_column($students, 'group'));
$groupsLabel = !empty($groupsList) ? implode(', ', $groupsList) : '—';
?>
<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($projectTitle) ?></title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1><?= htmlspecialchars($projectTitle) ?></h1>

        <!-- Крок 5 методички: вивід даних у вигляді HTML-таблиці циклом foreach -->
        <section class="card">
            <h2>Відомість успішності групи <?= htmlspecialchars($groupsLabel) ?></h2>

            <?php if ($totalStudents === 0): ?>
                <p>Список студентів порожній.</p>
            <?php else: ?>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>№</th>
                            <th>Студент (форматований запис)</th>
                            <th>Середній бал</th>
                            <th>Статус / мітка</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $index => $student): ?>
                            <?php
                            $description = formatStudent($student);
                            $status = getStudentStatus($student['avgGrade']);
                            $isExcellent = ($status === 'Відмінник');
                            ?>
                            <tr class="<?= $isExcellent ? 'row-excellent' : '' ?>">
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($description) ?></td>
                                <td><?= htmlspecialchars(number_format($student['avgGrade'], 2, '.', '')) ?></td>
                                <td>
                                    <span class="badge <?= $isExcellent ? 'badge-excellent' : 'badge-regular' ?>">
                                        <?= htmlspecialchars($status) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <!-- Крок 6 методички: окремий блок з агрегатним показником -->
        <section class="card summary-section">
            <h2>Підсумкові показники</h2>
            <p>Група: <strong><?= htmlspecialchars($groupsLabel) ?></strong></p>
            <p>Кількість студентів: <strong><?= $totalStudents ?></strong></p>
            <p>Кількість відмінників (бал ≥ 90): <strong><?= $excellentCount ?></strong></p>
            <p class="summary-main">
                Середній бал по групі:
                <strong><?= htmlspecialchars(number_format($averageGrade, 2, '.', '')) ?></strong>
            </p>
        </section>
    </div>
</body>

</html>