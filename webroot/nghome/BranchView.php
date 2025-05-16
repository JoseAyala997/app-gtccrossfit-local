<?php
include'connection.php';
$id = intval(mysqli_real_escape_string($conn,$_REQUEST['id']));

// Get branch details
$get_record = "SELECT b.*, 
    (SELECT COUNT(*) FROM gym_member WHERE branch_id = b.id) as total_members,
    (SELECT COUNT(*) FROM gym_member WHERE branch_id = b.id AND membership_status = 'Active') as active_members,
    (SELECT COUNT(*) FROM gym_member WHERE branch_id = b.id AND role_name = 'staff_member') as total_staff,
    (SELECT COUNT(*) FROM class_schedule WHERE branch_id = b.id AND is_active = 1) as active_classes
FROM gym_branch b WHERE b.id='$id'";

$select_query = $conn->query($get_record);
$result = array();

if(mysqli_num_rows($select_query) > 0){
    $result['status'] = '1';
    $result['error_code'] = 200;
    $result['error'] = custom_http_response_code(200);
    while($get_data = mysqli_fetch_assoc($select_query)){
        $result['result'][] = $get_data;
    }
} else {
    $result['status'] = '0';
    $result['error_code'] = 204;
    $result['error'] = custom_http_response_code(204);
    $result['result'] = array();
}

echo json_encode($result);
?>