<?php
session_start();
if(!isset($_SESSION["id"]))
{
    $_SESSION["id"] == 0;
}
if($_SESSION["id"] == 0) //If user isnt logged in we return them to login
{
    header("Location: Login.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Hub</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
    <div id="mainLay">
        <form method="post">
            <table class="mainPage">
                <tr>
                    <td>
                        <table class="heading">
                            <tr>
                                <td>
                                    <div id="userName">
                                        <?php
                                        if(isset($_SESSION["username"]))
                                        {
                                            echo htmlentities($_SESSION["username"]) . "'s Reservations"; //Displays users name in reservation page
                                        }
                                        else
                                        {
                                            echo "User";
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td>
                                    <label>Return to the <a href="Main.php">Main Page </a>here</label> <!-- Press to return to main page -->
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                    $server_name = "localhost";
                    $user_name = "root";
                    $password1 = "";
                    $db = "Librarydatabase";
                    $conn = new mysqli($server_name, $user_name, $password1, $db); //Connection to the database
                    if ($conn->connect_error) 
                    {
                        die("Connection Failed: " . htmlentities($conn->connect_error)); 
                    }            
                    $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription,reservations.ReservedDate 
                    FROM books JOIN categories ON books.CategoryID = categories.CategoryID JOIN reservations ON reservations.ISBN = books.ISBN WHERE Reservations.ISBN = Books.ISBN AND Reservations.username = '" . $_SESSION["username"] . "'"; //Select query that gets all books which have a reservation
                    $result = $conn->query($sql);
                    echo "<table class='currentData' border='1' cellpadding='10' style=\"background-color:turqoise\" >";
                    echo "<tr>
                            <th>ISBN</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Edition</th>
                            <th>Year</th>
                            <th>Genre</th>
                            <th>Date Reserved</th>
                            <th>Remove</th>
                        </tr>";

                    if ($result->num_rows > 0) //See if our select query returned rows
                    {
                        $CURRENT = 0; //Limit the page to 5 rows 
                        while ($row = $result->fetch_assoc()) 
                        {
                            if($CURRENT < 5) //Displays All reservations
                            {
                                echo "<tr>";
                                echo "<td>" . htmlentities($row["ISBN"]) . "</td>";
                                echo "<td>" . htmlentities($row["BookTitle"]) . "</td>";
                                echo "<td>" . htmlentities($row["Author"]) . "</td>";
                                echo "<td>" . htmlentities($row["Edition"]) . "</td>";
                                echo "<td>" . htmlentities($row["YearReleased"]) . "</td>";
                                echo "<td>" . htmlentities($row["CategoryDescription"]) . "</td>";
                                echo "<td>" . htmlentities($row["ReservedDate"]) . "</td>";
                                echo "<td><input class='ReservedButtons' type='submit' name='" . htmlentities($row["ISBN"]) . "' value='" . htmlentities($row["ISBN"]) . "'></td>";
                                echo "</tr>";
                                $CURRENT++;
                            }                        
                        }
                    } 
                    else 
                    {
                        echo "<tr><td colspan='6'>0 Reservations</td></tr>";
                    }
                    $result = $conn->query($sql);
                    while($rows = $result->fetch_assoc())
                    {
                        if(isset($_POST[$rows["ISBN"]]))
                        {          
                            try
                            {
                                $sqlInsert = "UPDATE books SET reserved = 'No' WHERE ISBN = '" . htmlentities($_POST[$rows["ISBN"]]) . "'";
                                $conn->query($sqlInsert);
                                $sqlInsert = "DELETE FROM reservations WHERE ISBN = '" . htmlentities($_POST[$rows["ISBN"]]) . "'";
                                $conn->query($sqlInsert);
                                echo "<script>alert('Removal successful for " . htmlentities($rows['BookTitle']) . "')</script>"; 
                                break;
                            }     
                            catch (mysqli_sql_exception $e)
                            {
                                if($e->getCode() === 1062 && $check == True)
                                {
                                    echo "<script>alert('Removed Already!')</script>";
                                }
                                elseif($check == True)
                                {
                                    echo  "<script>alert('An Error occured')</script>";
                                }
                            }
                        }
                    }
                    echo "</table>";
                    $conn->close(); //Close connection
                ?>
            </table>
        </form>
    </div>
</body>
</html>
