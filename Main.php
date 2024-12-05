<?php
session_start();
if(isset($_POST["LogOut"])) // Logging out
{
    $_SESSION["id"] = 0;
    session_destroy();
}
if(!isset($_SESSION["id"]))
{
    $_SESSION["id"] == 0;
}
// Redirect to login page if user is not authenticated
if($_SESSION["id"] == 0)
{
    header("Location: Login.php");
}
if(!isset($_SESSION["category"])) //Holds the current category for display
{
    $_SESSION["category"] = "all";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Hub</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS file -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
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
                                    <input type="submit" name="AllBooks" value="Reset Filters"> <!-- Returns to start and removes filters -->
                                </td>

                                <td>
                                    <input type="text" name="bookSearch"> <!-- Input for searching books -->
                                    <input type="submit" name="submitting" value="Search"> <!-- Submit search query -->
                                </td>
                                <td>
                                    <label for="category">Choose a genre:</label>
                                    <select id='optionDropdown' name="category" onchange="this.form.submit()"> <!-- Dropdown for selecting genre -->
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
                                                if($_POST["category"] != "all")
                                                {
                                                    $_SESSION["category"] = $_POST["category"]; //If Post category is selected we place it in session
                                                }
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
                    if (!isset($_SESSION['counter'])) 
                    {
                        $_SESSION['counter'] = 5; //If counter isnt set we set it to 5 as we want the first 5 books from our database
                    } 
                    if (isset($_POST['forwards'])) 
                    {
                        $_SESSION['counter'] += 5;  //If forwards button is pressed we increment counter by 5
                    }
                    if (isset($_POST['backwards']) and $_SESSION['counter'] >= 10) // Navigate backwards in pages
                    {
                        $_SESSION['counter'] -= 5; //if Backwards we decrement by 5
                    }
                    // Check for database connection errors
                    if ($conn->connect_error) 
                    {
                        die("Connection Failed: " . $conn->connect_error);
                    }
                    // Handle book search query
                    if(isset($_POST["submitting"])) //Searching by Author or Title
                    {
                        $bookSearch = $_POST["bookSearch"];
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM 
                        books JOIN categories ON books.CategoryID = categories.CategoryID WHERE BookTitle LIKE '%". $bookSearch. "%' OR Author LIKE '%". $bookSearch. "%'";
                        $_SESSION["counter"] = 5; #Session counter is set to 5 to display the first 5 from the author or title
                        $_SESSION["category"] = "all";
                    }   
                    else
                    {
                        // Default query to fetch all books
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM 
                        books JOIN categories ON books.CategoryID = categories.CategoryID";
                    }

                    // Filter books by selected genre
                    if($GenreSelected != "all" or $_SESSION["category"] != "all") //IF CATEGORY ISNT ALL WE DISPLAY THE GENRE
                    {
                        if($GenreSelected != "all")
                        {
                            $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM 
                            books JOIN categories ON books.CategoryID = categories.CategoryID WHERE categories.CategoryDescription =" . "'" . $GenreSelected . "'";
                            $_SESSION["counter"] = 5;
                        }
                        if($_SESSION["category"] != "all" and $GenreSelected == "all")
                        {
                            $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM
                             books JOIN categories ON books.CategoryID = categories.CategoryID WHERE categories.CategoryDescription =" . "'" . $_SESSION["category"] . "'";
                        }
                    }
                    
                    if(isset($_POST["AllBooks"])) //Top picks brings you back to the begining
                    {
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM 
                        books JOIN categories ON books.CategoryID = categories.CategoryID";
                        $_SESSION["counter"] = 5;
                        $_SESSION["category"] = "all";
                    }

                    $result = $conn->query($sql);

                    // Display books in a table format
                    echo "<table class='currentData' border='1' cellpadding='10'>";
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
                        // Going forwards and backwards with BEGIN and START which we initialised to 0 at the top
                        $BEGIN = $_SESSION["counter"] - 5;
                        $currentValue = $_SESSION["counter"];//Ensure the limit is 5 books per page, as we go to the next page we increment by 5 or decrement if Back
                        while ($row = $result->fetch_assoc() and $START < $currentValue) 
                        {
                            if($START >= $BEGIN)
                            {
                                echo "<tr>";
                                echo "<td>" . htmlentities($row["ISBN"]) . "</td>"; //html entities to prevent injection
                                echo "<td>" . htmlentities($row["BookTitle"]) . "</td>";
                                echo "<td>" . htmlentities($row["Author"]) . "</td>";
                                echo "<td>" . htmlentities($row["Edition"]) . "</td>";
                                echo "<td>" . htmlentities($row["YearReleased"]) . "</td>";
                                echo "<td>" . htmlentities($row["CategoryDescription"]) . "</td>";
                                
                                // Display reservation button or reserved status
                                if(htmlentities($row["Reserved"]) == "No")
                                {
                                    echo "<td>RESERVE : <input id='ReserveButtons' type='submit' name='" . $row["ISBN"] . "' value='" . $row["ISBN"] . "'></td>";
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
                        // Fordwards and backwards and Logging out
                        echo "<tr>";
                        echo "<td><input class='pagination' type='submit' name='backwards' value='<Back'>";
                        echo "<td><input class='pagination' type='submit' name='forwards' value='Next>'></td>";
                        echo "<td><input class ='pagination' type='submit' name='LogOut' value='Log out'</td>";
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
                        $servername5 = "localhost"; //Establishing a connection to count the amount of reservations for the specific user
                        $username5 = "root";
                        $password5 = "";
                        $dbname = "librarydatabase";

                        // Create connection
                        $conn5 = new mysqli($servername5, $username5, $password5, $dbname);

                        // Check connection
                        if ($conn5->connect_error) 
                        {
                            die("Connection failed: " . $conn->connect_error);
                        }

                        $sql5 = "SELECT COUNT(*) AS RESERVE_AMOUNT FROM Reservations WHERE Username = '" . $_SESSION["username"] . "'"; 
                        $ReserveAmount = $conn5->query($sql5);
                        $row5 = $ReserveAmount->fetch_assoc();
                        if(isset($_POST[$rows["ISBN"]])) //If reservation is pressed and limit isnt reached we insert the isbn into reservations and increment the amount of reservations
                        {      
                            if($row5["RESERVE_AMOUNT"] == 5) //If Reservation Limit reached we do not reserve 
                            {
                                echo "<script>alert('You reached your limit for Reservations!')</script>";
                                break;
                            }         
                            $date = date("Y-m-d");
                            try
                            {                               
                                $sqlInsert = "INSERT INTO RESERVATIONS VALUES('" . $rows['ISBN'] . "','" . $_SESSION["username"] . "','" . $date . "')"; // WE insert the reservation
                                $conn->query($sqlInsert);
                                $sqlInsert = "UPDATE Books SET Reserved = 'Yes' WHERE BookTitle = '" . $rows['BookTitle'] . "'"; //Updating the availability
                                $conn->query($sqlInsert);
                                echo "<script>alert('Reservation successful for " . $rows['BookTitle'] . " on " . $date .  "');</script>";                              
                            }
                            catch (mysqli_sql_exception $e) //Catches errors in database reservations
                            {
                                if($e->getCode() === 1062) //Duplicate error
                                {
                                    echo "<script>alert('Reserved Already! Check your Reservations')</script>";
                                }
                                else
                                {
                                    echo  "<script>alert('An Error occured')</script>";
                                }
                            }       
                            break;
                            $conn5->close();
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