<html>
    <head>
        <meta charset="UTF-8">
    <title>Midterm Exam</title>
    <link rel="stylesheet" href="myDesign.css">
    </head>
    <body>
        <?php include "headerlist.php"; ?>
        <?php
          $servername = "localhost";
          $username = "root";
          $password = "";
          $dbname = "examDB";
          
          try {
            /*Satır 16*/$conn = new PDO("mysql:host=$servername;", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
            echo "Database created successfully<br>";
          } catch (PDOException $e) {
                echo $e->getMessage();
          }
          $conn=null;
          
          try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "CREATE TABLE IF NOT EXISTS MyGuests (
                    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    firstname VARCHAR(30) NOT NULL,
                    lastname VARCHAR(30) NOT NULL,
                    email VARCHAR(50),
                    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    )";
            $conn->exec($sql);
            echo "Table MyGuests created successfully";$conn->exec("INSERT INTO MyGuests (firstname, lastname, email) VALUES ('Name 1', 'Last name 1', 'email1@example.com')");$conn->exec("INSERT INTO MyGuests (firstname, lastname, email) VALUES ('Name 2', 'Last name 2', 'email2@example.com')");
          } catch (PDOException $e) {
                echo $e->getMessage();
          }
          $conn = null;
        ?>

        <?php
        $errors1 = "";
        $errors2 = "";
        $errors3 = "";
        /*Satır 47*/if($_SERVER['REQUEST_METHOD'] == 'POST'){//Handling the form request
            /*Satır 48*/$fn = check_input($_POST["fname"]);//First name
            /*Satır 49*/$ln = check_input($_POST["lname"]);//Last Name
            /*Satır 50*/$em = check_input($_POST["email"]);//Email

            if (empty($fn)) {
                $errors1 = $errors1 . "* First name is required<br>";
            }
            if (!preg_match("/^[a-zA-Z .]*$/", $fn)) {
                $errors1 = $errors1 . "* Only letters and white space allowed<br>";
            }

            if (empty($ln)) {
                $errors2 = $errors2 . "* Last name is required<br>";
            }
            if (!preg_match("/^[a-zA-Z .]*$/", $ln)) {
                $errors2 = $errors2 . "* Only letters and white space allowed<br>";
            }

            if (empty($em)) {
                $errors3 = $errors3 . "* Email is required<br>";
            }
            if (!filter_var($em, FILTER_VALIDATE_EMAIL)) {
                $errors3 = $errors3 . "* Invalid email format<br>";
            }
        }
        ?>
        <h3>Add User</h3>
        <table>
        <form method = "post" action = "exam.php">
            <tr>
                <td>
                    First Name:<span  style='color: red;'>*</span>
                </td>
                <td>
                    <input type = "text" name = "fname" <?php if (isset($_POST['examform'])) {echo " value='" . $fn . "' ";}?>></input>
                </td>
                <td>
                    <?php echo "<span style='color: red;'>$errors1</span>"; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Last Name:<span  style='color: red;'>*</span>
                </td>
                <td>
                    <input type = "text" name = "lname" <?php if (isset($_POST['examform'])) {echo " value='" . $ln . "' ";}?>></input>
                </td>
                <td>
                    <?php echo "<span style='color: red;'>$errors2</span>"; ?>
                </td>
            </tr>
            <tr>
                <td>
                    Email:<span  style='color: red;'>*</span>
                </td>
                <td>
                    <input type = "text" name = "email" <?php if (isset($_POST['examform'])) {echo " value='" . $em . "' ";}?>></input>
                </td>
                <td>
                    <?php echo "<span style='color: red;'>$errors3</span>"; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input type = "submit"  value="Submit" name="examform"></input>
                </td>
            </tr>
        </form>
        </table>    
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "INSERT INTO MyGuests (firstname, lastname, email) VALUES ('$fn', '$ln', '$em')";
                $conn->exec($sql);
                echo "New user added successfully.<br>";
            } catch (PDOException $e) {
                echo $sql . "<br>" . $e->getMessage();
            }
            $conn = null;
        }
         
        echo "<br>";
        try {
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT id, firstname, lastname , email FROM MyGuests");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_NUM);

            echo "<table id='table_borders'>";
            echo "<th id='table_header'>ID</th>";
            echo "<th id='table_header'>First Name</th>";
            echo "<th id='table_header'>Last Name</th>";
            echo "<th id='table_header'>Email</th>";
            
            $index = 0;
            while ($row = $stmt->fetch()) {
                echo '<tr>';
                if ($index % 2 == 0) {
                    echo "<td id='table_even'>" . $row[0] . "</td>";
                    echo "<td id='table_even'>" . $row[1] . "</td>";
                    echo "<td id='table_even'>" . $row[2] . "</td>";
                    echo "<td id='table_even'>" . $row[3] . "</td>";
                    
                } else {
                    echo "<td id='table_odd'>" . $row[0] . "</td>";
                    echo "<td id='table_odd'>" . $row[1] . "</td>";
                    echo "<td id='table_odd'>" . $row[2] . "</td>";
                    echo "<td id='table_odd'>" . $row[3] . "</td>";
                    /*Satır 157*/
                }
                echo '</tr>';
                $index = $index + 1;
            }
            echo '</table>';
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        $conn = null;
        ?>
        <?php
        function check_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
        ?>
    </body>
</html>