<?php
// PDO - PHP Data Object for SQLite
try {
    $connection = new PDO("sqlite:./feedbackdb.db");
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tạo bảng nếu chưa tồn tại
    $connection->exec("
        CREATE TABLE IF NOT EXISTS feedback (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            body TEXT DEFAULT NULL,
            date DATETIME
        )
    ");
    
    // Chèn dữ liệu mẫu nếu bảng rỗng
    $count = $connection->query("SELECT COUNT(*) FROM feedback")->fetchColumn();
    if ($count == 0) {
        $connection->exec("
            INSERT INTO feedback (name, email, body, date) VALUES
            ('John', 'john@gmail.com', 'I like it', CURRENT_TIMESTAMP),
            ('Tony', 'tony12@gmail.com', 'Please add more videos', CURRENT_TIMESTAMP),
            ('Hoang', 'hoang@gmail.com', 'Let do Laravel project', CURRENT_TIMESTAMP)
        ");
    }
    
    // echo "Connected successfully to SQLite";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    $connection = null;
}
?>