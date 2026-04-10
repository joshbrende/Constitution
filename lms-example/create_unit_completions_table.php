<?php
/**
 * Run: php create_unit_completions_table.php
 * Creates unit_completions table in lms DB (use when artisan migrate fails).
 */
$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=lms;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS unit_completions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    unit_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_enrollment_unit (enrollment_id, unit_id),
    KEY unit_completions_user_id (user_id),
    KEY unit_completions_course_id (course_id),
    KEY unit_completions_unit_id (unit_id),
    KEY unit_completions_enrollment_id (enrollment_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

$pdo->exec($sql);
echo "Table unit_completions created or already exists.\n";
