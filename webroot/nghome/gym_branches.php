<?php
include("connection.php");

$query = "SELECT `id`, `name`, `address`, `phone`, `email`, `is_active` 
          FROM `gym_branch` 
          ORDER BY id = 1 DESC, name ASC";  // Central branch (id=1) always first
$res = $conn->query($query);
$result = array();

if ($res->num_rows > 0) {
    $result['status'] = '1';
    $result['error_code'] = 200;
    $result['error'] = custom_http_response_code(200);
    while($row = $res->fetch_assoc()) {
        $branch = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'address' => $row['address'],
            'phone' => $row['phone'],
            'email' => $row['email'],
            'is_active' => $row['is_active']
        );
        $result['result']['branches'][] = $branch;
    }
} else {
    $result['status'] = '0';
    $result['error_code'] = 204;
    $result['error'] = custom_http_response_code(204);
    $result['result'] = null;
}
echo json_encode($result);
?>