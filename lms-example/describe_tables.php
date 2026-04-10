<?php
/**
 * Run: php describe_tables.php
 * Outputs DESCRIBE for lms DB tables to schema.txt
 */
$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=lms;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$tables = [
    'users', 'courses', 'units', 'enrollments', 'course_progress',
    'quizzes', 'questions', 'quiz_attempts', 'course_categories',
    'course_category_pivot', 'course_codes', 'course_facilitators',
    'course_reviews', 'assignments', 'assignment_submissions',
    'certificates', 'learning_maps', 'roles', 'permissions',
    'model_has_roles', 'model_has_permissions', 'role_has_permissions',
];

$out = '';
foreach ($tables as $t) {
    try {
        $r = $pdo->query("DESCRIBE `$t`");
        $rows = $r->fetchAll(PDO::FETCH_ASSOC);
        $out .= "\n=== $t ===\n";
        foreach ($rows as $row) {
            $out .= sprintf("%-30s %-20s %s %s %s\n",
                $row['Field'],
                $row['Type'],
                $row['Null'],
                $row['Key'] ?: '-',
                $row['Default'] ?? 'NULL'
            );
        }
    } catch (Throwable $e) {
        $out .= "\n=== $t === SKIP: " . $e->getMessage() . "\n";
    }
}

file_put_contents(__DIR__ . '/schema.txt', $out);
echo "Written to schema.txt\n";
