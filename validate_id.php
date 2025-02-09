<?php
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

$sql = "SELECT * FROM Userinfo WHERE Userid = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_STR);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);


if ($row) {
    if ($row['Mobile'] === '1') {    
        echo json_encode(['existing' => true]);
    } else {
        echo json_encode(['success' => true, 'name' => $row['Name']]);
    }
} else {    
    echo json_encode(['success' => false]);
}
?>