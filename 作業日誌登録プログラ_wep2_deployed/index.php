<?php
session_start();

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    header('Location: menu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Wed 3</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        ul {
            line-height: 1.8;
        }
        a {
            text-decoration: none;
            color: blue;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <h1>Wed 2</h1>

    <p>以下は練習用のリンクです：</p>
    <ul>
        <li><a href="login.php">作業日誌登録プログラム</a></li>
        <li><a href="database_schema.html">データベース</a></li>
    </ul>

</body>
</html>
