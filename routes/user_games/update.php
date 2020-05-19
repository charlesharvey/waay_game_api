<?php

$id = $_GET['id'];
$json = file_get_contents('php://input');
// Converts it into a PHP object
$user_game_attributes = json_decode($json);

if (!empty($user_game_attributes)) {


    $updated = update_user_game($user_game_attributes);

    if ($updated) {


        $user_game = get_user_game($id);

        // TO DO IF EVERYONE READY SET GAME AS STARTED 1
        setGameAsStarted($user_game->game_id);


        http_response_code(200);
        echo json_encode($user_game);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error');
}
