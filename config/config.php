<?php 
$host ='localhost';
$dbname='companyappointment';
$username='root';
$password='';

try {
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo ' Connecting Error ' . $e->getMessage();
    exit;
}
function getUserById(int $id) {
    global $conn;  
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_uni_id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}