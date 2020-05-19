<?php


$json = file_get_contents('php://input');
// Converts it into a PHP object
$vote_attrs = json_decode($json);



if (!empty($vote_attrs->user_code)) {


    $game_id = $vote_attrs->game_id;
    $question_id = $vote_attrs->question_id;
    $answer_id = $vote_attrs->answer_id;
    $game_question = get_game_question($game_id, $question_id);
    $vote_attrs->game_question_id = $game_question->id;


    $user = get_user_from_code($vote_attrs->user_code);
    $vote_attrs->user_id = $user->id;


    $answer = get_answer($answer_id);


    if ($answer->correct) {
        $vote_attrs->score = 1;
    } else {
        $vote_attrs->score = 0;
    }

    $vote_id = create_vote($vote_attrs);


    if ($vote_id) {


        $vote = get_vote($vote_id);


        $votes = get_votes_for_game_question($game_question->id);
        $game_users = get_user_games_for_game($game_question->game_id);
        //  IF EVERYONE READY SET GAME AS VOTEED 1

        if (sizeof($votes)  == sizeof($game_users)  && sizeof($votes) > 0) {
            setGameQuestionAsVoted($game_question);
        }

        // ALSO CALCULATE EVERYONES SCORE
        foreach ($votes as $vt) {
            setAnswerScoreFromVotes($vt);
        } // end votes loop

        http_response_code(201);
        echo json_encode($vote);
    } else {
        http_response_code(404);
        echo json_encode('Error');
    }
} else {
    http_response_code(404);
    echo json_encode('Error 2');
}
