<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$game_attrs = json_decode($json);

if (!empty($game_attrs->user_code)) {


    $game_attrs->rounds_to_play = 5;
    $game_type = 1; /// 4 is michaels questions, 1 is my questions
    if (!empty($game_attrs->game_type)) {
        $game_type = $game_attrs->game_type;
    }

    $game_attrs->game_type  = $game_type;


    $game_id = create_game($game_attrs);

    if ($game_id) {

        //  ADD QUESTIONS TO GAMEQUESTIONS JOIN TABLE
        // AS MANY AS $game->rounds_to_play says
        $r = $game_attrs->rounds_to_play;
        $game_questions = create_game_questions($game_id, $game_type,  $r);


        // for ($i = 0; $i <  $r; $i++) {
        // }


        $game = get_game($game_id);

        // ADD USER WHO CREATED GAME TO GAME
        $game_user = create_game_user($game_id, $game->user_id);


        $game->questions = get_questions_for_game($game->id);
        $game->users = get_users_for_game($game->id);
        $game->answers = [];
        $game->votes = [];

        http_response_code(201);
        echo json_encode($game);
    } else {
        http_response_code(404);
        echo json_encode('Error 123');
    }
} else {
    http_response_code(404);
    echo json_encode('Error 2');
}
