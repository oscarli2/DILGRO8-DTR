<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['insert_user_id'];
    $record_date = $_POST['record_date'];

    // Handle optional times and format them correctly
    $arrival_am = !empty($_POST['arrival_am']) ? date("Y-m-d", strtotime($record_date)) . ' ' . date("H:i:s", strtotime($_POST['arrival_am'])) : null;
    $depart_am = !empty($_POST['depart_am']) ? date("Y-m-d", strtotime($record_date)) . ' ' . date("H:i:s", strtotime($_POST['depart_am'])) : null;
    $arrival_pm = !empty($_POST['arrival_pm']) ? date("Y-m-d", strtotime($record_date)) . ' ' . date("H:i:s", strtotime($_POST['arrival_pm'])) : null;
    $depart_pm = !empty($_POST['depart_pm']) ? date("Y-m-d", strtotime($record_date)) . ' ' . date("H:i:s", strtotime($_POST['depart_pm'])) : null;

    try {
        $conn->beginTransaction();

        if ($arrival_am) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, WorkType, AttFlag, OpenDoorFlag, mask) VALUES (:userid, :arrival_am, 0, 1, 0, 16, 1, 2)");
            $stmt->bindParam(':userid', $user_id, PDO::PARAM_STR);
            $stmt->bindParam(':arrival_am', $arrival_am, PDO::PARAM_STR);
            $stmt->execute();
        }

        if ($depart_am) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, WorkType, AttFlag, OpenDoorFlag, mask) VALUES (:userid, :depart_am, 1, 1, 0, 16, 1, 2)");
            $stmt->bindParam(':userid', $user_id, PDO::PARAM_STR);
            $stmt->bindParam(':depart_am', $depart_am, PDO::PARAM_STR);
            $stmt->execute();
        }

        if ($arrival_pm) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, WorkType, AttFlag, OpenDoorFlag, mask) VALUES (:userid, :arrival_pm, 0, 1, 0, 16, 1, 2)");
            $stmt->bindParam(':userid', $user_id, PDO::PARAM_STR);
            $stmt->bindParam(':arrival_pm', $arrival_pm, PDO::PARAM_STR);
            $stmt->execute();
        }

        if ($depart_pm) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, AttFlag, OpenDoorFlag, mask) VALUES (:userid, :depart_pm, 1, 1, 16, 1, 2)");
            $stmt->bindParam(':userid', $user_id, PDO::PARAM_STR);
            $stmt->bindParam(':depart_pm', $depart_pm, PDO::PARAM_STR);
            $stmt->execute();
        }

        $conn->commit();
        header("Location: secret.php");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
