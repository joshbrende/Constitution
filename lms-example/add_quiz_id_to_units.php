<?php
/**
 * Run: php add_quiz_id_to_units.php
 * Adds quiz_id column to units (use when artisan migrate fails).
 */
$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=lms;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$cols = $pdo->query("SHOW COLUMNS FROM units LIKE 'quiz_id'")->fetchAll();
if (empty($cols)) {
    $pdo->exec("ALTER TABLE units ADD COLUMN quiz_id BIGINT UNSIGNED NULL AFTER prerequisite_unit_id");
    $pdo->exec("ALTER TABLE units ADD INDEX units_quiz_id_index (quiz_id)");
    echo "quiz_id added to units.\n";
} else {
    echo "quiz_id already exists on units.\n";
}
