<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require('./dbconnect.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_id']) && $_SESSION['role'] === 'admin') {
        $id = $_POST['edit_id'];
        $work_date = $_POST['work_date'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_time = $_POST['end_time'] ?? '';
        $field_id = $_POST['field_id'] ?? '';
        $work_id = $_POST['work_id'] ?? '';
        $notes = $_POST['notes'] ?? '';

        // Kiểm tra và phân tách work_date
        $year = '';
        $month = '';
        $day = '';
        if ($work_date) {
            $date_parts = explode('-', $work_date);
            if (count($date_parts) === 3) {
                $year = $date_parts[0] ?? '';
                $month = $date_parts[1] ?? '';
                $day = $date_parts[2] ?? '';
                // Kiểm tra ngày không được ở tương lai
                $input_date = strtotime($work_date);
                $today = strtotime(date('Y-m-d'));
                if ($input_date > $today) {
                    $error = "未来の日付は選択できません。";
                }
            } else {
                $error = "日付の形式が正しくありません。";
            }
        } else {
            $error = "日付が選択されていません。";
        }

        // Phân tách start_time và end_time
        $shour = '';
        $smin = '';
        $ehour = '';
        $emin = '';
        if ($start_time) {
            list($shour, $smin) = explode(':', $start_time);
        } else {
            $error = "開始時間が選択されていません。";
        }
        if ($end_time) {
            list($ehour, $emin) = explode(':', $end_time);
        } else {
            $error = "終了時間が選択されていません。";
        }

        // Kiểm tra start_time < end_time
        if (!$error && $start_time && $end_time) {
            $start = strtotime($work_date . ' ' . $start_time);
            $end = strtotime($work_date . ' ' . $end_time);
            if ($start >= $end) {
                $error = "開始時間は終了時間より前でなければなりません。";
            }
        }

        // Kiểm tra lỗi trước khi cập nhật
        if (!$error) {
            try {
                $dbh->beginTransaction();
                $stmt = $dbh->prepare("UPDATE diary SET year = :year, month = :month, day = :day, shour = :shour, smin = :smin, ehour = :ehour, emin = :emin, field_id = :field_id, work_id = :work_id WHERE id = :id");
                $stmt->execute([
                    ':id' => $id,
                    ':year' => $year,
                    ':month' => $month,
                    ':day' => $day,
                    ':shour' => $shour,
                    ':smin' => $smin,
                    ':ehour' => $ehour,
                    ':emin' => $emin,
                    ':field_id' => $field_id,
                    ':work_id' => $work_id
                ]);

                // Cập nhật hoặc chèn notes
                $stmt = $dbh->prepare("SELECT COUNT(*) FROM notes WHERE diary_id = :diary_id");
                $stmt->execute([':diary_id' => $id]);
                if ($stmt->fetchColumn() > 0) {
                    $stmt = $dbh->prepare("UPDATE notes SET notes = :notes WHERE diary_id = :diary_id");
                } else {
                    $stmt = $dbh->prepare("INSERT INTO notes (diary_id, notes) VALUES (:diary_id, :notes)");
                }
                $stmt->execute([':diary_id' => $id, ':notes' => $notes]);

                $dbh->commit();
                $success = "データが正常に更新されました。";
            } catch (Exception $e) {
                $dbh->rollBack();
                $error = "データの更新中にエラーが発生しました: " . $e->getMessage();
            }
        }
    }
}

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
$diary_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>作業リスト編集</title>
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
        <h3 class="text-center mb-4">作業リスト編集</h3>
        <?php if ($error): ?>
            <div class="alert alert-danger text-center"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success text-center"><?php echo $success; ?></div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <th>ユーザー名</th>
                        <?php endif; ?>
                        <th>日付</th>
                        <th>開始時間</th>
                        <th>終了時間</th>
                        <th>場所</th>
                        <th>作業内容</th>
                        <th>備考</th>
                        <th>アクション</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diary_logs as $log): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($log['id']); ?></td>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <td><?php echo htmlspecialchars($log['user_name'] ?? ''); ?></td>
                        <?php endif; ?>
                        <td><?php echo htmlspecialchars(str_pad($log['year'], 4, '0', STR_PAD_LEFT) . '-' . str_pad($log['month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($log['day'], 2, '0', STR_PAD_LEFT)); ?></td>
                        <td><?php echo htmlspecialchars(str_pad($log['shour'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($log['smin'], 2, '0', STR_PAD_LEFT)); ?></td>
                        <td><?php echo htmlspecialchars(str_pad($log['ehour'], 2, '0', STR_PAD_LEFT) . ':' . str_pad($log['emin'], 2, '0', STR_PAD_LEFT)); ?></td>
                        <td><?php echo htmlspecialchars($log['field_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($log['work_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($log['notes'] ?? ''); ?></td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $log['id']; ?>">編集</button>
                        </td>
                    </tr>

                    <!-- Modal để chỉnh sửa -->
                    <div class="modal fade" id="editModal<?php echo $log['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $log['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel<?php echo $log['id']; ?>">作業データ編集 (ID: <?php echo htmlspecialchars($log['id']); ?>)</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($log['id']); ?>">
                                        <div class="mb-3">
                                            <label for="work_date<?php echo $log['id']; ?>" class="form-label">日付:</label>
                                            <input type="date" class="form-control" id="work_date<?php echo $log['id']; ?>" name="work_date" value="<?php echo htmlspecialchars(sprintf('%04d-%02d-%02d', $log['year'], $log['month'], $log['day'])); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="start_time<?php echo $log['id']; ?>" class="form-label">開始時間:</label>
                                            <input type="time" class="form-control" id="start_time<?php echo $log['id']; ?>" name="start_time" value="<?php echo htmlspecialchars(sprintf('%02d:%02d', $log['shour'], $log['smin'])); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="end_time<?php echo $log['id']; ?>" class="form-label">終了時間:</label>
                                            <input type="time" class="form-control" id="end_time<?php echo $log['id']; ?>" name="end_time" value="<?php echo htmlspecialchars(sprintf('%02d:%02d', $log['ehour'], $log['emin'])); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="field_id<?php echo $log['id']; ?>" class="form-label">場所:</label>
                                            <select class="form-select" id="field_id<?php echo $log['id']; ?>" name="field_id" required>
                                                <?php
                                                $field_stmt = $dbh->query("SELECT * FROM field");
                                                while ($field = $field_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($field['id'] == $log['field_id']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($field['id']) . '" ' . $selected . '>' . htmlspecialchars($field['name']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="work_id<?php echo $log['id']; ?>" class="form-label">作業内容:</label>
                                            <select class="form-select" id="work_id<?php echo $log['id']; ?>" name="work_id" required>
                                                <?php
                                                $work_stmt = $dbh->query("SELECT * FROM work");
                                                while ($work = $work_stmt->fetch(PDO::FETCH_ASSOC)) {
                                                    $selected = ($work['id'] == $log['work_id']) ? 'selected' : '';
                                                    echo '<option value="' . htmlspecialchars($work['id']) . '" ' . $selected . '>' . htmlspecialchars($work['shigoto']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="notes<?php echo $log['id']; ?>" class="form-label">備考:</label>
                                            <textarea class="form-control" id="notes<?php echo $log['id']; ?>" name="notes"><?php echo htmlspecialchars($log['notes'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary">更新</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="text-center mt-3"><a href="menu.php" class="btn btn-secondary">メニューに戻る</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>