<?php
    $hasError = false;
    if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
        echo "<div align=center><h5>You are already logged in</h5></div>";
    } else {
        if (isset($_POST['submit'])) {
            $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
            $password = trim($_POST['password']);

            if (empty($username) || empty($password)) {
                echo "<p style='color:red;'>Both fields are required.</p>";
            } else {
                $stmt = $conx->prepare("SELECT * FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $creds = $stmt->get_result()->fetch_assoc();

                if ($creds && password_verify($password, $creds['password'])) {
                    $_SESSION['username'] = $creds['username'];
                    $_SESSION['user_id'] = $creds['id'];
                    $_SESSION['admin'] = 1;

                    header("Location: index.php");
                    exit; 
                } else {
                    $hasError = true;
                }
            }
        } 
    }
?>
<div align=center>
    <fieldset style="width:300;">
        <legend><b>Login</b></legend>
        <form action="<?php echo $_SERVER['PHP_SELF']."?action=login"; ?>" method="post">
            <br>
            Username/Email: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            <br><input type="submit" name="submit" value="Login"><br>
        </form>
        <?php if ($hasError): ?>
            <p style="color: red;">Erabiltzailea edo pasahitza gaizki dago</p>
        <?php endif; ?>
    </fieldset>
</div>

