<?php
/**
 * Run: php create_attendance_registers_table.php
 * Creates attendance_registers table in lms DB (MyISAM, no FKs).
 */
$pdo = new PDO(
    'mysql:host=127.0.0.1;port=3306;dbname=lms;charset=utf8mb4',
    'root',
    '',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$sql = <<<'SQL'
CREATE TABLE IF NOT EXISTS attendance_registers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    course_id BIGINT UNSIGNED NOT NULL,
    unit_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    enrollment_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(50) NULL,
    name VARCHAR(255) NOT NULL,
    surname VARCHAR(255) NOT NULL,
    designation VARCHAR(255) NULL,
    organisation VARCHAR(255) NULL,
    contact_number VARCHAR(50) NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_enrollment_unit (enrollment_id, unit_id),
    KEY attendance_registers_course_id (course_id),
    KEY attendance_registers_unit_id (unit_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

$pdo->exec($sql);
echo "Table attendance_registers created or already exists.\n";
