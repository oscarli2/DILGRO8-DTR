<?php
include 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$password = $data['password']; // Hash the password
$email = $data['email'];
$name = $datas['name']

try {
    $sql = "UPDATE Userinfo SET Pwd = :password, Address = :email, Mobile = '1' WHERE Userid = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
