<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Records</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        .logout-button {
            background-color: #b62424 !important;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (!isset($_SESSION['userid'])) {
        header("Location: login.php");
        exit;
    }
    ?>
    <div class="main-container">
    <img src="https://drive.google.com/thumbnail?id=1rNAtss3r0fr-LtCZ9amzXejiw7RITArH" width="25%" style="display: block; margin-left: auto; margin-right: auto; margin-bottom:30px;">
        <div class="logout">
            <h1>View Attendance Records</h1>    
            <div class="logout-form"> 
                <form method="POST" action="logout.php">
                    <input class="logout-button" type ="submit" value="Logout">
                </form>
                <button class="dtrprint" onclick="window.print()">Print</button>
                <form method="POST" action="profile.php">
                    <input class="profile" type ="submit" value="Profile">
                </form>
            </div>   
        </div>
        <div class="container">
            <div class="form-container">
                <form method="POST" action="index.php">
                    <label for="user_id">Employee:</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">Select User</option>
                        <?php
                        include 'db_connection.php';
                        $logged_in_user_id = $_SESSION['userid'] ?? null;
                        $logged_in_user_admin = $_SESSION['admin'] ?? null;
                        if ($logged_in_user_admin > 0) {                              
                            try {
                                $userQuery = $conn->query("SELECT Userid, Name FROM Userinfo ORDER BY Name ASC"); 
                                while ($user = $userQuery->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = ($user['Userid'] == $logged_in_user_id) ? 'selected' : '';
                                    echo "<option value=\"" . ($user['Userid']) . "\" $selected>" . $user['Name'] . " - " . ($user['Userid']) . "</option>";
                                }
                            } catch (PDOException $e) {
                                echo "<option value=\"\">Error loading users</option>";
                            }
                        } else {
                            $selected = ($user['Userid'] == $logged_in_user_id) ? 'selected' : '';
                            $userQuery = $conn->query("SELECT Userid, Name FROM Userinfo WHERE Userid = '" . $logged_in_user_id ."'"); 
                            while ($user = $userQuery->fetch(PDO::FETCH_ASSOC)) {
                                $selected = ($user['Userid'] == $logged_in_user_id) ? 'selected' : '';
                                echo "<option value=\"" . ($user['Userid']) . "\" $selected>" . $user['Name'] . "</option>";
                            }
                        }                       
                        ?>
                    </select>
                    
                    <label for="start_date">Start Date:</label>
                    <input type="date" id="start_date" name="start_date" required>
                    
                    <label for="end_date">End Date:</label>
                    <input type="date" id="end_date" name="end_date" required>
                    
                    <input type="submit" value="View Records">
                </form>
            </div>
            <div class="table-container">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $user_id = $_POST['user_id'];
                    $start_date = $_POST['start_date'];
                    $end_date = $_POST['end_date'];

                    try {
                        $start_date = date('Y-m-d', strtotime($start_date));
                        $end_date = date('Y-m-d', strtotime($end_date));

                        $sql = "SET NOCOUNT ON;

                        EXEC sp_msforeachtable 'ALTER TABLE ? NOCHECK CONSTRAINT ALL';
                
                        WITH date_ranges AS (
                            SELECT CAST(? AS DATE) AS dt
                            UNION ALL
                            SELECT DATEADD(DAY, 1, dt) 
                            FROM date_ranges 
                            WHERE DATEADD(DAY, 1, dt) <= ?
                        )
                        SELECT 
                            DAY(date_ranges.dt) as 'days',
                            MAX(CASE WHEN T.Checktype = 0 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '03:00:00' AND '11:59:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS ArrivalAM,
                            MIN(CASE WHEN T.Checktype = 1 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '12:00:00' AND '14:00:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS DepartAM,
                            MAX(CASE WHEN T.Checktype = 0 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '12:00:00' AND '14:00:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS ArrivalPM,
                            MAX(CASE WHEN T.Checktype = 1 AND T.Userid = ? AND CAST(T.Checktime AS TIME) BETWEEN '14:00:01' AND '23:59:00' THEN FORMAT(T.Checktime, 'h:mm tt') END) AS DepartPM,
                            DATEPART(WEEKDAY, date_ranges.dt) AS Weekend
                        FROM 
                            date_ranges
                        LEFT JOIN 
                            Checkinout T ON date_ranges.dt = CAST(T.Checktime AS DATE)
                        GROUP BY 
                            date_ranges.dt;
                
                        EXEC sp_msforeachtable 'ALTER TABLE ? CHECK CONSTRAINT ALL'";

                        $stmt = $conn->prepare($sql);
                        $stmt->bindParam(1, $start_date, PDO::PARAM_STR);
                        $stmt->bindParam(2, $end_date, PDO::PARAM_STR);
                        $stmt->bindParam(3, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(4, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(5, $user_id, PDO::PARAM_INT);
                        $stmt->bindParam(6, $user_id, PDO::PARAM_INT);
                        $stmt->execute();
                        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($results) {
                            $logged_in_user_id = $_SESSION['userid'] ?? null;
                            $userQuery = $conn->query("SELECT Userid, Name FROM Userinfo WHERE Userid = '" . $logged_in_user_id ."'"); 
                            $user = $userQuery->fetch(PDO::FETCH_ASSOC);
                            $start_date = date('M d Y', strtotime($start_date));
                            $end_date = date('M d Y', strtotime($end_date));
                            echo "<h3>". ($user['Name']). " </h3>" . $start_date . " - " . $end_date;
                            echo "<table>";
                            echo "<tr><th>Date</th><th>Arrival AM</th><th>Departure AM</th><th>Arrival PM</th><th>Departure PM</th></tr>";
                            foreach ($results as $row) {
                                echo "<tr>";
                                echo "<td>" . ($row['days']) . "</td>";
                                echo "<td>" . ($row['ArrivalAM']) . "</td>";
                                echo "<td>" . ($row['DepartAM']) . "</td>";
                                echo "<td>" . ($row['ArrivalPM']) . "</td>";
                                echo "<td>" . ($row['DepartPM']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            echo "<p>No records found for the given dates.</p>";
                        }
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>
</html>
