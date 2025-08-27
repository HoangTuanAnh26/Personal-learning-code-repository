<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require('./dbconnect.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && $_SESSION['role'] === 'admin') {
    $id = $_POST['id'];
    try {
        $dbh->beginTransaction(); // Bắt đầu transaction để đảm bảo nhất quán
        // Xóa bản ghi trong notes trước (nếu có)
        $stmt = $dbh->prepare("DELETE FROM notes WHERE diary_id = :id");
        $stmt->execute([':id' => $id]);
        // Xóa bản ghi trong diary
        $stmt = $dbh->prepare("DELETE FROM diary WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $dbh->commit(); // Hoàn tất transaction
        header('Location: delete.php');
        exit;
    } catch (Exception $e) {
        $dbh->rollBack(); // Hoàn tác nếu có lỗi
        $error = "データの削除中にエラーが発生しました: " . $e->getMessage();
    }
} else {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $dbh->prepare("SELECT d.*, f.name AS field_name, w.shigoto AS work_name, u.userID AS user_name, n.notes 
                             FROM diary d 
                             LEFT JOIN field f ON d.field_id = f.id 
                             LEFT JOIN work w ON d.work_id = w.id 
                             LEFT JOIN user u ON d.user_id = u.id
                             LEFT JOIN notes n ON d.id = n.diary_id
                             ORDER BY d.year DESC, d.month DESC, d.day DESC");
        $stmt->execute();
    } else {
        $stmt = $dbh->prepare("SELECT d.*, f.name AS field_name, w.shigoto AS work_name, n.notes 
                             FROM diary d 
                             LEFT JOIN field f ON d.field_id = f.id 
                             LEFT JOIN work w ON d.work_id = w.id 
                             LEFT JOIN notes n ON d.id = n.diary_id
                             WHERE d.user_id = :user_id
                             ORDER BY d.year DESC, d.month DESC, d.day DESC");
        $stmt->execute([':user_id' => $_SESSION['user_id']]);
    }
    $work_logs = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>作業データの削除</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
        }
        table {
            min-width: 900px;
        }
    }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <h3 class="text-center mb-4">作業データの削除</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($_SESSION['role'] !== 'admin'): ?>
            <p class="text-center">あなたは削除権限を持っていません。リスト表示のみ可能です。</p>
            <p class="text-center mt-3"><a href="list.php" class="btn btn-secondary">リスト表示へ</a></p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>ユーザー名</th>
                            <th>日付</th>
                            <th>開始時間</th>
                            <th>終了時間</th>
                            <th>合計時間</th>
                            <th>場所</th>
                            <th>作業内容</th>
                            <th>備考</th>
                            <th>アクション</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($work_logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <td><?php echo htmlspecialchars($log['user_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars("$log[year]-" . str_pad($log['month'], 2, '0', STR_PAD_LEFT) . "-" . str_pad($log['day'], 2, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php echo htmlspecialchars(str_pad($log['shour'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($log['smin'], 2, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php echo htmlspecialchars(str_pad($log['ehour'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($log['emin'], 2, '0', STR_PAD_LEFT)); ?></td>
                            <td><?php
                                $start = new DateTime("$log[year]-$log[month]-$log[day] $log[shour]:$log[smin]");
                                $end = new DateTime("$log[year]-$log[month]-$log[day] $log[ehour]:$log[emin]");
                                $interval = $start->diff($end);
                                $hours = $interval->h + ($interval->i / 60);
                                echo number_format($hours, 2); ?> 時間
                            </td>
                            <td><?php echo htmlspecialchars($log['field_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['work_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($log['notes'] ?? ''); ?></td>
                            <td>
                                <form method="post" onsubmit="return confirmDelete(event)">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($log['id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <p class="text-center mt-3"><a href="menu.php" class="btn btn-secondary">メニューに戻る</a></p>
    </div>
    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function confirmDelete(event) {
            if (!confirm('このデータを削除しますか？')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>