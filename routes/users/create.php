<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$user_attrs = json_decode($json);

if (!empty($user_attrs->nickname)) {


    $user_id = create_user($user_attrs);

    if ($user_id) {

        $user = get_user($user_id);

        http_response_code(201);
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error 2');
}
