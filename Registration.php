
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-image:url('Librarian.jfif'); " >
    <div class="RegistrationDetails">
        <form method="post">
            <input type="text" name="username" placeholder="Username"></input>
            <br>
            <input type="password" name="password" placeholder="Password"></input>
            <br>
            <input type="password" name="passwordconfirmation" placeholder="Password Confirmation"></input>
            <br>
            <input type="email" name="email" placeholder="Email Adress"></input>
            <br>
            <input type="tel" name="phone" placeholder="Phone Number For Example : (012 345 6789)"></input>
            <br>
            <input type="text" name="FirstName" placeholder="Forename"></input>
            <br>
            <input type="text" name="LastName" placeholder="Surname"></input>
            <br>
            <input type="text" name="House" placeholder="Home Adress"></input>
            <br>
            <input type="text" name="Town" placeholder="Town"></input>
            <br>
            <input type="text" name="County" placeholder="County"></input>
            <br>
            <button type="submit" name="submit">Register</button>
            <p style="text-align:center;">RETURN TO LOGIN PAGE <a href="Login.php">HERE</a></p>
        </form> 
    </div>
    <?php
        $check = True;
        $server_name = "localhost";
        $username = "root";
        $password = "";
        $db = "librarydatabase";
        $conn = new mysqli($server_name,$username,$password,$db);
        if($conn->connect_error)
        {
            die("Connection Failed : ". $conn->connect_error);
        }
        if($_SERVER["REQUEST_METHOD"] === "POST")
        {
            if (empty($PasswordConfirmation) === NULL) 
            {
                echo "<script>alert('Password confirmation cannot be empty!')</script>";
            }
            try
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
                    $Username = $_POST["username"];
                    $Password = $_POST["password"];
                    $PasswordConfirmation = $_POST["passwordconfirmation"];
                    $phone = $_POST["phone"];
                    $email = $_POST["email"];
                    $FirstName = $_POST["FirstName"];
                    $LastName = $_POST["LastName"];
                    $House = $_POST["House"];
                    $Town = $_POST["Town"];
                    $County = $_POST["County"];
                    if($Password !== $PasswordConfirmation)
                    {
                        echo "<script>alert('Passwords do not match!')</script>";
                    }
                    elseif(strlen($Password) !== 6)
                    {
                        echo "<script>alert('Password must be of length 6!')</script>";
                    } 
                    elseif (strlen((string)$phone) !== 10)
                    {
                        echo "<script>alert('Telephone Number must be of length 10!')</script>";
                    }
                    else
                    {
                        $sql = "INSERT INTO users (Username, Password, Email, Phone, FirstName, LastName, House, Town, County)
                        VALUES ('$Username', '$Password', '$email', '$phone', '$FirstName', '$LastName', '$House', '$Town', '$County')";
                        $conn->query($sql);
                        echo "<script>alert('Your account has been successfully created!');</script>";
                    }
                }
            }
            catch (mysqli_sql_exception $e)
            {

                if($e->getCode() === 1062 && $check == True)
                {
                    echo "<script>alert('Username or Number you entered already exists')</script>";
                }
                elseif($check == True)
                {
                    echo  "<script>alert('An Error occured')</script>";
                }
            }
        }
        $conn->close();
    ?>
    </body>
</html>
