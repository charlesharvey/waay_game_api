<?php


// add user to game


$json = file_get_contents('php://input');
// Converts it into a PHP object
$user_game_attrs = json_decode($json);


if (!empty($user_game_attrs->user_code)) {


    $user = get_user_from_code($user_game_attrs->user_code);
    $user_game_attrs->user_id = $user->id;
    $game = get_game_from_code($user_game_attrs->game_code);
    $user_game_attrs->game_id = $game->id;


    $user_game_id = create_user_game($user_game_attrs);



    if ($user_game_id) {




        http_response_code(201);
        echo json_encode($game);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error 2');
}
