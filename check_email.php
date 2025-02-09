<?php
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];

$sql = "SELECT Address FROM Userinfo WHERE Address = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}
?>
