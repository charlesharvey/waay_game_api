<?php

$id = $_GET['id'];
$json = file_get_contents('php://input');
// Converts it into a PHP object
$answer_attributes = json_decode($json);

if (!empty($answer_attributes)) {

    $user = get_user_from_code($answer_attributes->user_code);
    $user_id = $user->id;
    $game_id = $answer_attributes->game_id;
    $question_id = $answer_attributes->question_id;


    $updated = mark_answer_as_complete($user_id, $game_id, $question_id);

    if ($updated) {

        setGameQuestionAsFinished($game_id, $question_id);


        http_response_code(200);
        echo json_encode(true);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error');
}
