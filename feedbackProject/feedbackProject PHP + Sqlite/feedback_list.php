<?php
require 'components/header.php';
?>
<div class="container mt-4">
    <h1>List of feedbacks here</h1>
    <?php
    $sql = "SELECT name, email, body FROM feedback;";
    if ($connection != null) {
        try {
            $statement = $connection->prepare($sql);
            $statement->execute();
            $statement->setFetchMode(PDO::FETCH_ASSOC);
            $feedbacks = $statement->fetchAll();
            
            echo '<ul class="list-group">';
            foreach ($feedbacks as $feedback) {
                $name = $feedback['name'] ?? '';
                $email = $feedback['email'] ?? '';
                $body = $feedback['body'] ?? '';
                echo "<li class='list-group-item'>";
                echo "<p>" . htmlspecialchars($name) . "</p>";
                echo "<p>" . htmlspecialchars($email) . "</p>";
                echo "<p>" . htmlspecialchars($body) . "</p>";
                echo "</li>";
            }
            echo '</ul>';
        } catch (PDOException $e) {
            echo "<p class='text-danger'>Cannot query data. Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p class='text-danger'>No database connection.</p>";
    }
    ?>
    <a href="index.php" class="btn btn-primary mt-3">Back to form</a>
</div>
<?php
include 'components/footer.php';
?>