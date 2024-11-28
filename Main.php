<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Hub</title>
    <link rel="stylesheet" href="style.css">
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
                                    <input type="submit" name="AllBooks" value="Top Picks">                           
                                </td>

                                <td>
                                    <input type="text" name="bookSearch">
                                    <input type="submit" name="submitting" value="Search">
                                </td>
                                <td>
                                    <label for="category">Choose a genre:</label>
                                    <select name="category" onchange="this.form.submit()">
                                        <option value="all">GENRE</option>
                                        <?php
                                            $server_name = "localhost";
                                            $user_name = "root";
                                            $password1 = "";
                                            $db = "Librarydatabase";
                                            $conn1 = new mysqli($server_name, $user_name, $password1, $db);
                                            if ($conn1->connect_error) 
                                            {
                                                die("Connection Failed: " . $conn1->connect_error);
                                            }
                                            $sql1 = "SELECT CategoryDescription FROM categories";                                           
                                            $result1 = $conn1->query($sql1);
                                            $i = 0;
                                            while ($row = $result1->fetch_assoc()) 
                                            {
                                                $Categories[$i] = $row["CategoryDescription"];
                                                echo "<option value=\"$Categories[$i]\">" . htmlentities($row["CategoryDescription"]) . "</option>";
                                                $i += 1;
                                            }
                                            $GenreSelected = "all";
                                            if(isset($_POST["category"]))
                                            {
                                                $GenreSelected = $_POST["category"]; 
                                            }                                        
                                        ?>
                                    </select>
                                </td>   
                                <td>
                                    Reservations : <a href="Reservations.php">Here</a>
                                </td>
                                <td>
                                    <div id="userName">
                                        <?php
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
                    $START = 0;
                    $server_name = "localhost";
                    $user_name = "root";
                    $password1 = "";
                    $db = "Librarydatabase";
                    $conn = new mysqli($server_name, $user_name, $password1, $db);

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
                    if (isset($_POST['backwards']) and $_SESSION['counter'] >= 10) //Pages
                    {
                        $_SESSION['counter'] -= 5; 
                    }
                

                    if ($conn->connect_error) 
                    {
                        die("Connection Failed: " . $conn->connect_error);
                    }
                    if(isset($_POST["submitting"]))
                    {
                        $bookSearch = $_POST["bookSearch"];
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID WHERE BookTitle LIKE '%". $bookSearch. "%' OR Author LIKE '%". $bookSearch. "%'";
                        $_SESSION["counter"] = 5;
                    }   
                    else
                    {
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID";
                    }
                    if($GenreSelected != "all")
                    {
                        $sql = "SELECT books.ISBN,books.BookTitle,books.Author,books.Edition,books.YearReleased,books.CategoryID,books.Reserved,categories.CategoryDescription FROM books JOIN categories ON books.CategoryID = categories.CategoryID WHERE categories.CategoryDescription =" . "'" . $GenreSelected . "'";
                        $_SESSION["counter"] = 5;
                    }
                    $result = $conn->query($sql);

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
                        $BEGIN = $_SESSION["counter"] - 5;
                        $currentValue = $_SESSION["counter"];
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
                        echo "<tr>";
                        echo "<td><input type='submit' name='backwards' value='<Back'>";
                        echo "<td><input type='submit' name='forwards' value='Next>'></td>";
                        echo "</tr>";

                    } 
                    else 
                    {
                        echo "<tr><td colspan='6'>0 Results</td></tr>";
                    }
                    $result = $conn->query($sql);
                    while($rows = $result->fetch_assoc())
                    {
                        if($_SESSION["Reservations"] == 5)
                        {
                            echo "<script>alert('You reached your limit for Reservations!')</script>";
                            break;
                        }
                        if(isset($_POST[$rows["ISBN"]]))
                        {               
                            $date = date("Y-m-d");
                            try
                            {                               
                                $sqlInsert = "INSERT INTO RESERVATIONS VALUES('" . $rows['ISBN'] . "','" . $_SESSION["username"] . "','" . $date . "')";
                                $conn->query($sqlInsert);
                                $sqlInsert = "UPDATE Books SET Reserved = 'Yes' WHERE BookTitle = '" . $rows['BookTitle'] . "'";
                                $conn->query($sqlInsert);
                                echo "<script>alert('Reservation successful for " . $rows['BookTitle'] . " on " . $date .  "');</script>";  
                                $_SESSION["Reservations"] += 1;                               
                            }
                            catch (mysqli_sql_exception $e)
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