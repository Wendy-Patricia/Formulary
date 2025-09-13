<?php
// Initialize message variable
$message = "";

// Check if the form was submitted

// Database connection parameters
$host = 'localhost';
$dbname = 'to_do_list';
$username = 'root';
$password = '';

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle connection error
    die("Connection error: " . $e->getMessage());
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["add_task"])) {
        // Get and validate form data
        $name = trim($_POST["name"] ?? '');
        $priority = $_POST["priority"] ?? '';

        if (empty($name) || empty($priority)) {
            // Error message if required fields are empty
            $message = "Fill in all required fields!";
        } else {
            // Prepare and execute the INSERT statement safely
            $stmt = $pdo->prepare("INSERT INTO tasks (name, priority) VALUES (:name, :priority)");
            $stmt->execute([
                ':name' => $name,
                ':priority' => $priority
            ]);
            // Success message
            $message = "Task added successfully!";
        }
    } else if (isset($_POST["clear_tasks"])) {
        // Clear form action (if needed)
        $stmt = $pdo->prepare("DELETE FROM tasks");
        $stmt->execute();
        $message = "All tasks cleared!";
    } else if (isset($_POST["reset"])) {
        // Reset form action
        $message = "Form cleared!";
    } else if (isset($_POST["delete_task"])) {
        // Delete a specific task
        $task_id = $_POST["task_id"];
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->execute([':id' => $task_id]);
        $message = "Task deleted!";
    }
}
// Fetch all tasks from the database
$stmt = $pdo->prepare("SELECT * FROM tasks ORDER BY 
    CASE priority 
        WHEN 'high' THEN 1 
        WHEN 'medium' THEN 2 
        WHEN 'low' THEN 3 
    END, created_at DESC");
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>To Do List</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>To Do List</h1>

        <!-- Display message -->
        <?php if (!empty($message)) : ?>
            <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Task form -->
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
            <div class="form-group">
                <label for="name">Task Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter task name" required />
            </div>

            <div class="form-group">
                <label>Priority:</label>
                <div class="priority-group">
                    <div class="priority-option">
                        <input type="radio" id="high" name="priority" value="high" required>
                        <label for="high">High</label>
                    </div>

                    <div class="priority-option">
                        <input type="radio" id="medium" name="priority" value="medium" checked>
                        <label for="medium">Medium</label>
                    </div>

                    <div class="priority-option">
                        <input type="radio" id="low" name="priority" value="low">
                        <label for="low">Low</label>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <input type="submit" name="add_task" value="Add Task" class="btn btn-primary" />
                <input type="submit" name="clear_tasks" value="Clear All Tasks" class="btn btn-clear"
                    onclick="return confirm('Are you sure you want to clear all tasks?');" />
            </div>
        </form>

        <!-- Task list -->
        <div class="task-list">
            <h2>Your Tasks</h2>

            <?php if (count($tasks) > 0) : ?>
                <?php foreach ($tasks as $task) : ?>
                    <div class="task-item <?php echo $task['priority'] . '-priority'; ?>">
                        <div class="task-info">
                            <div class="task-name <?php echo $task['is_completed'] ? 'completed' : ''; ?>">
                                <?php echo htmlspecialchars($task['name']); ?>
                            </div>
                            <div class="task-priority">
                                Priority: <?php echo ucfirst($task['priority']); ?> |
                                Created: <?php echo date('M j, Y g:i A', strtotime($task['created_at'])); ?>
                            </div>
                        </div>
                        <div class="task-actions">
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="display: inline;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                            </form>
                            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" style="display: inline;">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="delete_task" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this task?');">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="no-tasks">
                    No tasks yet. Add a task to get started!
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

</html>