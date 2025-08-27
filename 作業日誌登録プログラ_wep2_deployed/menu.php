<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理メニュー</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h3 class="card-title mb-4">管理メニューへようこそ</h3>
                        <p class="mb-4">機能を選択してください:</p>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <a href="add.php" class="btn btn-primary mb-2 w-100">1. データ追加</a>
                            <a href="edit.php" class="btn btn-primary mb-2 w-100">2. データ編集</a>
                            <a href="delete.php" class="btn btn-primary mb-2 w-100">3. データ削除</a>
                            <a href="list.php" class="btn btn-primary mb-2 w-100">4. リスト表示</a>
                            <a href="logout.php" class="btn btn-danger mb-2 w-100">5. ログアウト</a>
                        <?php else: ?>
                            <a href="add.php" class="btn btn-primary mb-2 w-100">1. データ追加</a>
                            <a href="list.php" class="btn btn-primary mb-2 w-100">2. リスト表示</a>
                            <a href="logout.php" class="btn btn-danger mb-2 w-100">3. ログアウト</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>