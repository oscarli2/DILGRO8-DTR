<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST['insert_user_id'];
    $record_date = $_POST['record_date'];
    $arrival_am = $_POST['arrival_am'] ? $_POST['record_date'] . ' ' . $_POST['arrival_am'] : null;
    $depart_am = $_POST['depart_am'] ? $_POST['record_date'] . ' ' . $_POST['depart_am'] : null;
    $arrival_pm = $_POST['arrival_pm'] ? $_POST['record_date'] . ' ' . $_POST['arrival_pm'] : null;
    $depart_pm = $_POST['depart_pm'] ? $_POST['record_date'] . ' ' . $_POST['depart_pm'] : null;

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
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
