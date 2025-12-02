<?php
$servername = "db"; // In Docker, we use the service name, not 'localhost'
$username = "user";
$password = "test";
$dbname = "todo_db";

// Connect to Database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle Form Submit (Add Task)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task = $_POST['task'];
    $sql = "INSERT INTO tasks (task) VALUES ('$task')";
    $conn->query($sql);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id=$id");
}
?>

<!DOCTYPE html>
<html>
<head><title>Docker To-Do</title></head>
<body>
    <h1>My DevOps To-Do List</h1>
    <form method="post">
        <input type="text" name="task" placeholder="New Task" required>
        <button type="submit">Add</button>
    </form>
    <ul>
        <?php
        $result = $conn->query("SELECT * FROM tasks");
        while($row = $result->fetch_assoc()) {
            echo "<li>" . $row['task'] . " <a href='?delete=" . $row['id'] . "'>[x]</a></li>";
        }
        ?>
    </ul>
</body>
</html>
