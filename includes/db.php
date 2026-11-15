<?php
// Creates one PDO connection to the SQLite database for every page.

$databasePath = __DIR__ . '/../database/rewards.db';

try {
    $pdo = new PDO('sqlite:' . $databasePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
} catch (PDOException $exception) {
    // Don't show database details to the user.
    http_response_code(500);
    exit('The gift card database is unavailable. Please try again later.');
}
