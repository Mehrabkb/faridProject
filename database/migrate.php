<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

$db = $config['db'];

try {

    $pdo = new PDO(
        "mysql:host={$db['host']};dbname={$db['name']};charset={$db['charset']}",
        $db['user'],
        $db['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );

} catch (PDOException $e) {

    die("DB Connection Failed: " . $e->getMessage());
}

$pdo->exec("
CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) UNIQUE,
    run_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

$migrationPath = __DIR__ . '/migrations';

$files = scandir($migrationPath);

$files = array_filter($files, function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'sql';
});

sort($files);

$stmt = $pdo->query("SELECT migration FROM migrations");

$executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($files as $file) {

    if (in_array($file, $executed)) {
        continue;
    }

    echo "Running: {$file}\n";

    $sql = file_get_contents($migrationPath . '/' . $file);

    try {

        $pdo->beginTransaction();

        $pdo->exec($sql);

        $stmt = $pdo->prepare("
        INSERT INTO migrations (migration)
        VALUES (?)
    ");

        $stmt->execute([$file]);

        $pdo->commit();

        echo "Success: {$file}\n";

    } catch (Exception $e) {

        // چک کن transaction فعال است یا نه
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        echo "Migration Failed: {$file}\n";
        echo $e->getMessage() . "\n";

        exit;
    }

}

echo "All migrations completed\n";
