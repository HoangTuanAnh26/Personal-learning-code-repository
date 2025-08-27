<?php
session_start(); // Khởi động session để kiểm tra và hủy

// Xử lý khi người dùng gửi form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_destroy(); // Hủy session
    header('Location: login.php'); // Chuyển hướng về trang đăng nhập
    exit; // Dừng script
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログアウト</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-4">ログアウト確認</h3>
                        <p>本当にログアウトしますか？</p>
                        <div class="d-flex justify-content-center gap-2">
                            <!-- Form để gửi yêu cầu logout -->
                            <form method="post" onsubmit="return confirm('ログアウトします。よろしいですか？');">
                                <button type="submit" class="btn btn-danger">ログアウト</button>
                            </form>
                            <a href="menu.php" class="btn btn-secondary">メニューに戻る</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>