<?php




$id = ($_GET['id']);


if (is_numeric($id) > 0) {
    $game = get_game($id);
} else {
    $game = get_game_from_code($id);
}

if ($game) {
    $game->game_questions = get_game_questions_for_game($game->id);
    $game->questions = get_questions_for_game($game->id);
    $game->user_games = get_user_games_for_game($game->id);
    $game->users = get_users_for_game($game->id);
    $game->answers = get_answers_for_game($game->id);
    $game->votes = get_votes_for_game($game->id);





    $game->id = intval($game->id);

    echo json_encode($game);
} else {
    http_response_code(404);
    echo json_encode('error');
}
