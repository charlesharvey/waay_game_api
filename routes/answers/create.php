<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$answer_attrs = json_decode($json);


if (!empty($answer_attrs->user_code)) {


    $user = get_user_from_code($answer_attrs->user_code);
    $answer_attrs->user_id = $user->id;


    $answer_id = create_answer($answer_attrs);



    if ($answer_id) {


        $answer = get_answer($answer_id);

        // TO DO IF EVERYONE READY SET GAME AS ANSWERED 1
        setGameQuestionAsAnswered($answer_attrs->game_id, $answer_attrs->question_id);


        http_response_code(201);
        echo json_encode($answer);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error 2');
}
