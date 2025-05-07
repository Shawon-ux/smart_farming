<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // You can use database-stored credentials for better security
    if ($username === "admin" && $password === "admin123") {
        $_SESSION["admin"] = true;
        header("Location: admin_panel.php");
        exit();
    } else {
        $error = "Invalid login credentials!";
    }
}
?>

<form method="POST">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <input type="submit" value="Login">
</form>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
