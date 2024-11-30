<?php
session_start();
if(isset($_POST["LogOut"])) // Logging out
{
    $_SESSION["id"] = 0;
}
// Redirect to login page if user is not authenticated
if($_SESSION["id"] == 0)
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
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS file -->
</head>
<body>
    <div id="mainLay">
        <form method="post"> <!-- Form for handling user input and actions -->
            <table class="mainPage">
                <tr>
                    <td>
                        <table class="heading">
                            <tr>
                                <td>                               
                                    <input type="submit" name="AllBooks" value="Top Picks"> <!-- Button for displaying all books -->
                                </td>

                                <td>
                                    <input type="text" name="bookSearch"> <!-- Input for searching books -->
                                    <input type="submit" name="submitting" value="Search"> <!-- Submit search query -->
                                </td>
                                <td>
                                    <label for="category">Choose a genre:</label>
                                    <select name="category" onchange="this.form.submit()"> <!-- Dropdown for selecting genre -->
                                        <option value="all">GENRE</option>
                                        <?php
                                            // Database connection for fetching categories
                                            $server_name = "localhost";
                                            $user_name = "root";
                                            $password1 = "";
                                            $db = "Librarydatabase";
                                            $conn1 = new mysqli($server_name, $user_name, $password1, $db);
                                            if ($conn1->connect_error) 
                                            {
                                                die("Connection Failed: " . $conn1->connect_error);
                                            }
                                            
                                            // Fetching all categories from the database
                                            $sql1 = "SELECT CategoryDescription FROM categories";                                           
                                            $result1 = $conn1->query($sql1);
                                            $i = 0;
                                            while ($row = $result1->fetch_assoc()) 
                                            {
                                                $Categories[$i] = $row["CategoryDescription"];
                                                echo "<option value=\"$Categories[$i]\">" . htmlentities($row["CategoryDescription"]) . "</option>";
                                                $i += 1;
                                            }
                                            
                                            // Handling genre selection
                                            $GenreSelected = "all";
                                            if(isset($_POST["category"]))
                                            {
                                                $GenreSelected = $_POST["category"]; 
                                            }                                        
                                        ?>
                                    </select>
                                </td>   
                                <td>
                                    Reservations : <a href="Reservations.php">Here</a> <!-- Link to reservations page -->
                                </td>
                                <td>
                                    <div id="userName">
                                        <?php
                                        // Displaying the username or a default label
                                        if(isset($_SESSION["username"]))
                                        {
                                            echo $_SESSION["username"]; 
                                        }
                                        else
                                        {
                                            echo "User";
                                        }
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                    // Initialize default values for pagination
                    $START = 0;
                    $server_name = "localhost";
                    $user_name = "root";
                    $password1 = "";
                    $db = "Librarydatabase";
                    $conn = new mysqli($server_name, $user_name, $password1, $db);

                    // Initialize session variables for reservations and pagination
                    if(!isset($_SESSION["Reservations"]))
                    {
                        $_SESSION["Reservations"] = 0;
                    }
                    if (!isset($_SESSION['counter'])) 
                    {
                        $_SESSION['counter'] = 5; 
                    } 
                    if (isset($_POST['forwards'])) 
                    {
                        $_SESSION['counter'] += 5; 
                    }
                    if (isset($_POST['backwards']) and $_SESSION['counter'] >= 10) // Navigate backwards in pages
                    {
                        $_SESSION['counter'] -= 5; 
                    }
                    // Check for database connection errors
                    if ($conn->connect_error) 
                    {
                        die("Connection Failed: " . $conn->connect_error);
                    }

                    // Handle book search query
                    if(isset($_POST["submitting"]))
                    {
                        $bookSearch = $_POST["bookSearch"];
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID WHERE BookTitle LIKE '%". $bookSearch. "%' OR Author LIKE '%". $bookSearch. "%'";
                        $_SESSION["counter"] = 5;
                    }   
                    else
                    {
                        // Default query to fetch all books
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID";
                    }

                    // Filter books by selected genre
                    if($GenreSelected != "all")
                    {
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID WHERE categories.CategoryDescription =" . "'" . $GenreSelected . "'";
                        $_SESSION["counter"] = 5;
                    }

                    $result = $conn->query($sql);

                    // Display books in a table format
                    echo "<table border='1' cellpadding='10'>";
                    echo "<tr>
                            <th>ISBN</th>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>Edition</th>
                            <th>Year</th>
                            <th>Genre</th>
                            <th>Reservations</th>
                        </tr>";

                    if ($result->num_rows > 0) 
                    {
                        // Implement pagination logic
                        $BEGIN = $_SESSION["counter"] - 5;
                        $currentValue = $_SESSION["counter"];//Ensure the limit is 5 books per page, as we go to the next page we increment by 5 or decrement if Back
                        while ($row = $result->fetch_assoc() and $START < $currentValue) 
                        {
                            if($START >= $BEGIN)
                            {
                                echo "<tr>";
                                echo "<td>" . htmlentities($row["ISBN"]) . "</td>";
                                echo "<td>" . htmlentities($row["BookTitle"]) . "</td>";
                                echo "<td>" . htmlentities($row["Author"]) . "</td>";
                                echo "<td>" . htmlentities($row["Edition"]) . "</td>";
                                echo "<td>" . htmlentities($row["YearReleased"]) . "</td>";
                                echo "<td>" . htmlentities($row["CategoryDescription"]) . "</td>";
                                
                                // Display reservation button or reserved status
                                if(htmlentities($row["Reserved"]) == "No")
                                {
                                    echo "<td>RESERVE : <input type='submit' name='" . $row["ISBN"] . "' value='" . $row["ISBN"] . "' style='background:transparent; border-width:0.1px; color:white'></td>";
                                }
                                else
                                {
                                    echo "<td>RESERVED</td>";
                                }
                                echo "</tr>";
                            }
                            $START++;
                            if($row === NULL)
                            {
                                break;
                            }
                        }
                        // Pagination buttons
                        echo "<tr>";
                        echo "<td><input type='submit' name='backwards' value='<Back'>";
                        echo "<td><input type='submit' name='forwards' value='Next>'></td>";
                        echo "<td><input type='submit' name='LogOut' value='Log out'</td>";
                        echo "</tr>";

                    } 
                    else 
                    {
                        echo "<tr><td colspan='6'>0 Results</td></tr>";
                    }

                    // Handle book reservations
                    $result = $conn->query($sql);
                    while($rows = $result->fetch_assoc())
                    {
                        if($_SESSION["Reservations"] == 4) //If Reservation Limit reached we do not reserve 
                        {
                            echo "<script>alert('You reached your limit for Reservations!')</script>";
                            break;
                        }
                        if(isset($_POST[$rows["ISBN"]])) //If reservation is pressed and limit isnt reached we insert the isbn into reservations and increment the amount of reservations
                        {               
                            $date = date("Y-m-d");
                            try
                            {                               
                                $sqlInsert = "INSERT INTO RESERVATIONS VALUES('" . $rows['ISBN'] . "','" . $_SESSION["username"] . "','" . $date . "')"; // WE insert the reservation
                                $conn->query($sqlInsert);
                                $sqlInsert = "UPDATE Books SET Reserved = 'Yes' WHERE BookTitle = '" . $rows['BookTitle'] . "'"; //Updating the availability
                                $conn->query($sqlInsert);
                                echo "<script>alert('Reservation successful for " . $rows['BookTitle'] . " on " . $date .  "');</script>";  
                                $_SESSION["Reservations"] += 1;                               
                            }
                            catch (mysqli_sql_exception $e) //Catches errors in database reservations
                            {
                                if($e->getCode() === 1062)
                                {
                                    echo "<script>alert('Reserved Already! Check your Reservations')</script>";
                                }
                                elseif($check == True)
                                {
                                    echo  "<script>alert('An Error occured')</script>";
                                }
                            }       
                            break;
                        }
                    }
                    echo "</table>";
                    $conn->close();
                ?>
            </table>
        </form>
    </div>
</body>
</html>