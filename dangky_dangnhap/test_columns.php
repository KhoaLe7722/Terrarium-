<?php
require 'config.php';
$stmt = $conn->query("SHOW COLUMNS FROM users");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . "\n";
}
