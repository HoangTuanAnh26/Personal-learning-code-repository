<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require('./dbconnect.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $work_date = $_POST['work_date'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $field_id = $_POST['field_id'] ?? '';
    $work_id = $_POST['work_id'] ?? '';
    $notes = $_POST['notes'] ?? '';

    // Lấy ngày hiện tại (10:03 AM JST, 03/07/2025)
    $current_date = date('Y-m-d');

    // Kiểm tra dữ liệu
    if (!$work_date) {
        $error = "日付を選択してください。";
    } elseif (!$start_time) {
        $error = "開始時間を選択してください。";
    } elseif (!$end_time) {
        $error = "終了時間を選択してください。";
    } elseif (!$field_id) {
        $error = "場所を選択してください。";
    } elseif (!$work_id) {
        $error = "作業内容を選択してください。";
    } else {
        // Phân tách ngày và giờ
        $date_parts = explode('-', $work_date);
        if (count($date_parts) === 3) {
            $year = $date_parts[0];
            $month = $date_parts[1];
            $day = $date_parts[2];
        } else {
            $error = "日付の形式が正しくありません。";
        }

        list($shour, $smin) = explode(':', $start_time);
        list($ehour, $emin) = explode(':', $end_time);

        // Kiểm tra thời gian
        if ($shour !== '' && $smin !== '' && $ehour !== '' && $emin !== '') {
            $start_datetime = DateTime::createFromFormat('Y-m-d H:i', "$work_date $start_time");
            $end_datetime = DateTime::createFromFormat('Y-m-d H:i', "$work_date $end_time");
            if ($start_datetime && $end_datetime) {
                if ($end_datetime <= $start_datetime) {
                    $error = "終了時刻は開始時刻より後である必要があります。";
                }
            } else {
                $error = "時刻の形式が正しくありません。";
            }
        } else {
            $error = "時刻が正しく入力されていません。";
        }

        // Kiểm tra ngày (không cho phép ngày trong tương lai)
        if ($work_date > $current_date && !$error) {
            $error = "未来の日付は選択できません。";
        }
    }

    if (!$error) {
        try {
            $dbh->beginTransaction();
            $stmt = $dbh->prepare("INSERT INTO diary (user_id, year, month, day, shour, smin, ehour, emin, field_id, work_id) VALUES (:user_id, :year, :month, :day, :shour, :smin, :ehour, :emin, :field_id, :work_id)");
            $stmt->execute([
                ':user_id' => $_SESSION['user_id'],
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

            $diary_id = $dbh->lastInsertId();
            if ($notes) {
                $stmt = $dbh->prepare("INSERT INTO notes (diary_id, notes) VALUES (:diary_id, :notes)");
                $stmt->execute([':diary_id' => $diary_id, ':notes' => $notes]);
            }

            $dbh->commit();
            header('Location: list.php');
            exit;
        } catch (Exception $e) {
            $dbh->rollBack();
            $error = "データの追加中にエラーが発生しました: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規データ追加</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="d-flex justify-content-center align-items-center min-vh-100 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">新規作業データ追加</h3>
                        <form method="post" onsubmit="return validateTime()">
                            <div class="mb-3">
                                <label for="work_date" class="form-label">日付:</label>
                                <input type="date" class="form-control" id="work_date" name="work_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_time" class="form-label">開始時間:</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_time" class="form-label">終了時間:</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                            <div class="mb-3">
                                <label for="field_id" class="form-label">場所:</label>
                                <select class="form-select" id="field_id" name="field_id" required>
                                    <option value="">-- 選択 --</option>
                                    <?php
                                    $stmt = $dbh->query("SELECT * FROM field");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="work_id" class="form-label">作業内容:</label>
                                <select class="form-select" id="work_id" name="work_id" required>
                                    <option value="">-- 選択 --</option>
                                    <?php
                                    $stmt = $dbh->query("SELECT * FROM work");
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['shigoto']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">備考:</label>
                                <textarea class="form-control" id="notes" name="notes"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">追加</button>
                            <div class="text-danger text-center mt-2"><?php echo $error; ?></div>
                        </form>
                        <p class="text-center mt-3"><a href="menu.php" class="btn btn-secondary">メニューに戻る</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function validateTime() {
            let start = document.querySelector('input[name="start_time"]').value;
            let end = document.querySelector('input[name="end_time"]').value;
            let errorDiv = document.querySelector('.text-danger');
            if (start && end && start >= end) {
                errorDiv.textContent = '終了時刻は開始時刻より後である必要があります！';
                return false;
            }
            errorDiv.textContent = ''; // Xóa lỗi cũ
            return true;
        }
    </script>
</body>

</html>