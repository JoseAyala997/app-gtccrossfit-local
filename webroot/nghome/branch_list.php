<?php
include("connection.php");
$sql = "SELECT * FROM `gym_branch` ORDER BY id = 1 DESC, name ASC"; // Central branch (id=1) first
$result1 = $conn->query($sql);
if ($result1->num_rows > 0) {
    $result['status'] = '1';
    $result['error_code'] = 200;
    $result['error'] = custom_http_response_code(200);
    while($row = $result1->fetch_assoc()) {
        // Add statistics for each branch
        $branch_id = $row['id'];
        $stats_sql = "SELECT 
            (SELECT COUNT(*) FROM gym_member WHERE branch_id = $branch_id) as total_members,
            (SELECT COUNT(*) FROM gym_member WHERE branch_id = $branch_id AND membership_status = 'Active') as active_members,
            (SELECT COUNT(*) FROM gym_member WHERE branch_id = $branch_id AND role_name = 'staff_member') as total_staff";
        $stats_result = $conn->query($stats_sql);
        $stats = $stats_result->fetch_assoc();
        
        // Merge branch info with statistics
        $row = array_merge($row, $stats);
        $result['result'][] = $row;
    }
} else {
    $result['status'] = '0';
    $result['error_code'] = 204;
    $result['error'] = custom_http_response_code(204);
    $result['result'] = array();
}
echo json_encode($result);
$conn->close();
?>