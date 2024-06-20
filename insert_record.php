<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['insert_user_id'];
    $record_date = $_POST['record_date'];
    $arrival_am = date_format($_POST['record_date'],"Y-m-d") . ' ' . date_format($_POST['arrival_am'],"H:i:s");
    $depart_am = date_format($_POST['record_date'],"Y-m-d") . ' ' . date_format($_POST['depart_am'],"H:i:s");
    $arrival_pm = date_format($_POST['record_date'],"Y-m-d") . ' ' . date_format($_POST['arrival_pm'],"H:i:s");
    $depart_pm = date_format($_POST['record_date'],"Y-m-d") . ' ' . date_format($_POST['depart_pm'],"H:i:s");

    try {
        $conn->beginTransaction();

        if ($arrival_am) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, AttFlag, OpenDoorFlag, mask) VALUES (?, ?, 0, 1, 16, 1, 2)");
            $stmt->execute([$user_id, $arrival_am]);
        }

        if ($depart_am) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, AttFlag, OpenDoorFlag, mask) VALUES (?, ?, 1, 1, 16, 1, 2)");
            $stmt->execute([$user_id, $depart_am]);
        }

        if ($arrival_pm) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, AttFlag, OpenDoorFlag, mask) VALUES (?, ?, 0, 1, 16, 1, 2)");
            $stmt->execute([$user_id, $arrival_pm]);
        }

        if ($depart_pm) {
            $stmt = $conn->prepare("INSERT INTO Checkinout (Userid, Checktime, Checktype, Sensorid, AttFlag, OpenDoorFlag, mask) VALUES (?, ?, 1, 1, 16, 1, 2)");
            $stmt->execute([$user_id, $depart_pm]);
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
