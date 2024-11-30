<?php
session_start(); // Start the session to maintain user state
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to the external CSS file for styling -->
</head>
<body>
    <div class="wrapped">
        <form method="post"> <!-- Form for login input -->
            <h1>MATOUS BOOKS</h1>
            <div class="inputBox">
                <input type="text" name="username" placeholder="Username"> <!-- Username input field -->
            </div>
            <div class="inputBox">
                <input type="password" name="password" placeholder="Password"> <!-- Password input field -->
            </div>
            <button type="submit" name="submit">Login</button> <!-- Login button -->
            <div class="registerLink">
                <p>OR <a href="Registration.php">REGISTER</a> NOW!</p> <!-- Link to the registration page -->
            </div>
        </form>
    </div>
    <?php
        // Initialize session variables for user state
        $_SESSION["id"] = 0;
        $check = True; // Flag to validate form completeness

        // Handle form submission
        if(isset($_POST["submit"]))
        {
            foreach ($_POST as $key => $value)
            {
                // Check if any form field is empty, except the submit button
                if(empty($value) && $key != "submit")
                {
                    $message = "You are missing fields!";
                    echo "<script>alert('$message')</script>"; // Alert the user about missing fields
                    $check = False; // Mark validation as failed
                    break;
                }
            }

            // Proceed if form validation is successful
            if($check)
            {
                // Database connection details
                $server_name = "localhost";
                $username = "root";
                $password = "";
                $db = "librarydatabase";

                // Establish connection to the database
                $conn = new mysqli($server_name, $username, $password, $db);

                // Retrieve user input from the form
                $UsernameSite = $_POST["username"];
                $PasswordSite = $_POST["password"];

                // SQL query to check user credentials using prepared statements
                $query = "SELECT * FROM USERS WHERE username = ? AND password = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $UsernameSite, $PasswordSite);
                $stmt->execute();
                $result = $stmt->get_result();

                // Verify if the user credentials match a record in the database
                if($result->num_rows > 0)
                {
                    $_SESSION["id"] = 1; // Mark the user as logged in
                    $_SESSION["username"] = $UsernameSite; // Store the username in the session
                    header("Location: Main.php"); // Redirect to the main page
                    exit();
                }
                else
                {
                    // Alert the user if login details are incorrect
                    echo "<script>alert('Incorrect Details')</script>";
                }
            }
        }
    ?>
</body>
</html>
