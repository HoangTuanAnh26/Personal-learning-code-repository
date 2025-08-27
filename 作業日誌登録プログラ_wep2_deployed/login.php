<?php
session_start();
require('./dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $dbh->prepare("SELECT * FROM user WHERE userID = :userID");
    $stmt->execute([':userID' => $userID]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: menu.php');
        exit;
    } else {
        $error = "ユーザーIDまたはパスワードが間違っています。";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

</head>

<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">システムログイン</h3>
                        <form method="post">
                            <div class="mb-3">
                                <label for="userID" class="form-label">ユーザーID:</label>
                                <input type="text" class="form-control" id="userID" name="userID" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">パスワード:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">ログイン</button>
                        </form>
                        <?php if (isset($error)): ?>
                            <p class="text-danger text-center mt-3"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <p class="text-center mt-3">アカウントをお持ちではありませんか？<a href="register.php">今すぐ登録</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>