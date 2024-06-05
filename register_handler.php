<?php
$hostname = '172.20.72.124';
$dbname = 'anviz';
$username = 'sa';
$password = 'CDPabina';
$mssqldriver = '{ODBC Driver 11 for SQL Server}';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST['userid'];
    $pwd = $_POST['password'];

    try {
        $conn = new PDO("odbc:Driver=$mssqldriver;Server=$hostname;Database=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check if the user ID exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Userinfo WHERE Userid = :userid");
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $userExists = $stmt->fetchColumn();

        if ($userExists) {
            // Update the password for the user
            $stmt = $conn->prepare("UPDATE Userinfo SET Pwd = :pwd WHERE Userid = :userid");
            $stmt->bindParam(':pwd', $pwd, PDO::PARAM_STR);
            $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
            $stmt->execute();

            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, "message" => "User ID not found."));
        }
    } catch (PDOException $e) {
        echo json_encode(array("success" => false, "message" => $e->getMessage()));
    }
}
?>
