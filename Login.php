<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapped">
        <form method="post">
            <h1>MATOUS BOOKS</h1>
            <div class="inputBox">
                <input type="text" name="username" placeholder="Username">
            </div>
            <div class="inputBox">
                <input type="password" name="password" placeholder="Password">
            </div>
            <button type="submit" name="submit">Login</button> 
            <div class="registerLink">
                <p>OR <a href="Registration.php">REGISTER</a> NOW!</p>
            </div>
        </form>
    </div>
    <?php
        $check = True;
        if(isset($_POST["submit"]))
        {
            foreach ($_POST as $key => $value)
            {
                if(empty($value) && $key != "submit")
                {
                    $message = "You are missing fields!";
                    echo "<script>alert('$message')</script>";
                    $check = False;
                    break;
                }
            }
            if($check)
            {
                $server_name = "localhost";
                $username = "root";
                $password = "";
                $db = "librarydatabase";
                $conn = new mysqli($server_name,$username,$password,$db);
                $UsernameSite = $_POST["username"];
                $PasswordSite = $_POST["password"];
                $query = "SELECT * FROM USERS WHERE username = ? AND password = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss",$UsernameSite,$PasswordSite);
                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0)
                {
                    
                    $_SESSION["username"] = $UsernameSite;
                    header("Location: Main.php");
                    exit();
                }
                else
                {
                    echo"<script>alert('Incorrect Details')</script>";
                }
            }
        }
    ?>
</body>
</html>