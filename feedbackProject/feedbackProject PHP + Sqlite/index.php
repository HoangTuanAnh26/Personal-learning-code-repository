<?php
require 'components/header.php';
$name = $email = $body = '';
$name_error = $email_error = $body_error = '';

if (isset($_POST['submit'])) {
    // Validations
    if (empty($_POST['name'])) {
        $name_error = 'Name is required';
    } else {
        $name = htmlspecialchars($_POST['name']);
    }

    if (empty($_POST['email'])) {
        $email_error = 'Email is required';
    } else {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    }
    
    if (empty($_POST['body'])) {
        $body_error = 'Feedback is required';
    } else {
        $body = filter_input(INPUT_POST, 'body', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    }
    
    $validate_success = empty($name_error) && empty($email_error) && empty($body_error);

    if ($validate_success && $connection != null) {
        $sql = "INSERT INTO feedback (name, email, body) VALUES (?, ?, ?)";
        try {
            $statement = $connection->prepare($sql);
            $statement->bindParam(1, $name);
            $statement->bindParam(2, $email);
            $statement->bindParam(3, $body);
            $statement->execute();
            header("Location: feedback_list.php");
            exit;
        } catch (PDOException $e) {
            echo "Cannot insert feedback into database. Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container mt-4">
    <h1>Enter your feedback here</h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <div class="mb-3">
            <input type="text" class="form-control <?php echo $name_error ? 'is-invalid' : ''; ?>" name="name" placeholder="What is your name?" value="<?php echo htmlspecialchars($name); ?>">
            <p class="text-danger"><?php echo $name_error; ?></p>
        </div>
        <div class="mb-3">
            <input type="email" class="form-control <?php echo $email_error ? 'is-invalid' : ''; ?>" name="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>">
            <p class="text-danger"><?php echo $email_error; ?></p>
        </div>
        <div class="mb-3">
            <textarea class="form-control <?php echo $body_error ? 'is-invalid' : ''; ?>" name="body" placeholder="Enter your feedback" rows="2"><?php echo htmlspecialchars($body); ?></textarea>
            <p class="text-danger"><?php echo $body_error; ?></p>
        </div>
        <div class="mb-3">
            <input name="submit" type="submit" class="btn btn-primary" value="Send">
        </div>
    </form>
</div>

<?php include 'components/footer.php'; ?>