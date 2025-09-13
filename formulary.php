<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Acess with $_POST
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    $priority = $_POST["priority"]; // Access the selected radio button value

    // Validation of required fields
    if (empty($nome) || empty($email) || empty($senha)) {
        echo "❌ fill in all required fields!";
    } else {
        echo "✅ form submitted successfully!";
        // show all data here
        echo "<br>Priority: " . htmlspecialchars($priority); // Display the selected priority
    }
}
?>

<!-- HTML -->

<!doctype html>
<html>

<body>
    <h4>To do list</h4>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <input type="text" name="nome" placeholder="Name Task" /><br>

        <p>Priority:</p>
        <input type="radio" id="high" name="priority" value="high">
        <label for="high">High</label><br>

        <input type="radio" id="medium" name="priority" value="medium">
        <label for="medium">Medium</label><br>

        <input type="radio" id="low" name="priority" value="low">
        <label for="low">Low</label><br>

        <input type="submit" value="Add Task"/>
        <input type="reset" value="Clear"/>
    </form>
</body>

</html>