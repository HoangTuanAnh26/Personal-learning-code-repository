<?php
session_start();
require('./dbconnect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Kiểm tra mật khẩu khớp
    if (strlen($userID) > 50) {
        $error = "ユーザーIDは50文字以内にしてください。";
    } elseif (strlen($password) > 100) {
        $error = "パスワードは100文字以内にしてください。";
    } elseif ($password !== $confirmPassword) {
        $error = "パスワードと確認用パスワードが一致しません。";
    } else {
        // Kiểm tra userID đã tồn tại
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM user WHERE userID = :userID");
        $stmt->execute([':userID' => $userID]);
        if ($stmt->fetchColumn() > 0) {
            $error = "ユーザーIDは既に存在します。別のユーザーIDを選択してください。";
        } else {
            // Mã hóa mật khẩu và thêm vào cơ sở dữ liệu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $dbh->prepare("INSERT INTO user (userID, password, role) VALUES (:userID, :password, :role)");
            $stmt->execute([
                ':userID' => $userID,
                ':password' => $hashedPassword,
                ':role' => 'user'
            ]);
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">アカウント登録</h3>
                        <form method="post" onsubmit="return validateForm()">
                            <div class="mb-3">
                                <label for="userID" class="form-label">ユーザーID:</label>
                                <input type="text" class="form-control" id="userID" name="userID" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">パスワード:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">パスワードの確認:</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                            </div>
                            <div class="error text-danger text-center mb-3" style="display: none;"></div>
                            <button type="submit" class="btn btn-primary w-100">登録</button>
                        </form>
                        <?php if (isset($error)): ?>
                            <p class="text-danger text-center mt-3"><?php echo $error; ?></p>
                        <?php endif; ?>
                        <p class="text-center mt-3"><a href="login.php">ログインに戻る</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function validateForm() {
            let password = document.querySelector('#password').value;
            let confirmPassword = document.querySelector('#confirmPassword').value;
            let errorDiv = document.querySelector('.error');
            
            if (password !== confirmPassword) {
                errorDiv.textContent = 'パスワードと確認用パスワードが一致しません！';
                errorDiv.style.display = 'block';
                return false;
            }
            
            // Kiểm tra độ dài userID và password
            let userID = document.querySelector('#userID').value;
            if (userID.length > 50) {
                errorDiv.textContent = 'ユーザーIDは50文字以内にしてください。';
                errorDiv.style.display = 'block';
                return false;
            }
            if (password.length > 100) {
                errorDiv.textContent = 'パスワードは100文字以内にしてください。';
                errorDiv.style.display = 'block';
                return false;
            }
            
            errorDiv.style.display = 'none';
            return true;
        }
    </script>
</body>
</html>