<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require('./dbconnect.php');

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
$work_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>作業リスト</title>
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
        <h3 class="text-center mb-4">作業リスト</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <th>ユーザー名</th>
                        <?php endif; ?>
                        <th>日付</th>
                        <th>開始時間</th>
                        <th>終了時間</th>
                        <th>合計時間</th>
                        <th>場所</th>
                        <th>作業内容</th>
                        <th>備考</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($work_logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <td><?php echo htmlspecialchars($log['user_name']); ?></td>
                            <?php endif; ?>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-center mt-3"><a href="menu.php" class="btn btn-secondary">メニューに戻る</a></p>
    </div>
    <!-- Bootstrap 5 JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<!-- Bảng nó cứ đen đen kiểu gì, muốn chuyển sang xanh dương -->

<!-- Vẫn chưa hiểu ý nghĩa đoạn này
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
} -->