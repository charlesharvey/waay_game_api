<?php


function get_answer($answer_id = null) {
    global $conn;
    if ($answer_id != null) {

        try {
            $query = "SELECT * FROM answers WHERE answers.id = :id LIMIT 1";
            $answer_query = $conn->prepare($query);
            $answer_query->bindParam(':id', $answer_id);
            $answer_query->setFetchMode(PDO::FETCH_OBJ);
            $answer_query->execute();

            $answer_count = $answer_query->rowCount();

            if ($answer_count == 1) {
                $answer =  $answer_query->fetch();
                $answer =  processAnswer($answer);
            } else {
                $answer = null;
            }
            unset($conn);
            return $answer;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if answer id is not greated than 0
        return null;
    }
}

function get_answers_for_question($game_id, $question_id) {
    global $conn;

    $query = "SELECT *  FROM answers   WHERE game_id = :game_id AND
    question_id = :question_id  ORDER BY  answers.id DESC";

    try {

        $answers_query = $conn->prepare($query);
        $answers_query->setFetchMode(PDO::FETCH_OBJ);
        $answers_query->bindParam(':game_id', $game_id);
        $answers_query->bindParam(':question_id', $question_id);
        $answers_query->execute();
        $answers_count = $answers_query->rowCount();

        if ($answers_count > 0) {
            $answers =  $answers_query->fetchAll();
            $answers = processAnswers($answers);
        } else {
            $answers =  [];
        }

        unset($conn);
        return $answers;
    } catch (PDOException $err) {
        return [];
    };
}

function get_answers_for_game($game_id) {
    global $conn;

    $query = "SELECT *  FROM answers   WHERE game_id = :game_id   ORDER BY  answers.id DESC";

    try {

        $answers_query = $conn->prepare($query);
        $answers_query->setFetchMode(PDO::FETCH_OBJ);
        $answers_query->bindParam(':game_id', $game_id);
        $answers_query->execute();
        $answers_count = $answers_query->rowCount();

        if ($answers_count > 0) {
            $answers =  $answers_query->fetchAll();
            $answers = processAnswers($answers);
        } else {
            $answers =  [];
        }

        unset($conn);
        return $answers;
    } catch (PDOException $err) {
        return [];
    };
}


function mark_answer_as_complete($user_id, $game_id, $question_id) {

    global $conn;
    $finished = 1;
    if ($question_id) {
        try {
            $query = "UPDATE answers SET 
            `finished` = :finished WHERE
             game_id = :game_id AND 
             user_id = :user_id AND 
             question_id = :question_id  ";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':finished', $finished);
            $user_query->bindParam(':user_id', $user_id);
            $user_query->bindParam(':game_id', $game_id);
            $user_query->bindParam(':question_id', $question_id);
            $user_query->execute();
            unset($conn);
            return true;
        } catch (PDOException $err) {
            return false;
        };
    } else { // user name was blank
        return false;
    }
}



function getcorrects($answer) {
    return $answer->correct;
}

function getfinisheds($answer) {
    return $answer->finished;
}


function setGameQuestionAsFinished($game_id, $question_id) {
    $answers = get_answers_for_question($game_id, $question_id);
    $game_users = get_user_games_for_game($game_id);

    $finisheds = array_map('getfinisheds', $answers);
    $game_question_ready = true;
    foreach ($finisheds as $r) {
        if ($r == false) {
            $game_question_ready = false;
        }
    }

    if (sizeof($answers)  == sizeof($game_users)  && $game_question_ready) {
        global $conn;
        $query = "UPDATE game_questions SET 
        `finished` = :finished
        WHERE game_questions.game_id = :game_id AND
         game_questions.question_id = :question_id ";

        try {
            $finished = 1;
            $questions_query = $conn->prepare($query);
            $questions_query->setFetchMode(PDO::FETCH_OBJ);
            $questions_query->bindParam(':finished', $finished);
            $questions_query->bindParam(':game_id', $game_id);
            $questions_query->bindParam(':question_id', $question_id);
            $questions_query->execute();
            $questions_count = $questions_query->rowCount();
            if ($questions_count > 0) {
                $game_question =  $questions_query->fetch();
                $game_question = processGameQuestion($game_question);
            } else {
                $game_question =  null;
            }
            unset($conn);
            return $game_question;
        } catch (PDOException $err) {
            return [];
        };
    } else {
        return false; // not all people havent answered yet
    }
}



function setGameQuestionAsAnswered($game_id, $question_id) {

    $answers = get_answers_for_question($game_id, $question_id);
    $game_users = get_user_games_for_game($game_id);

    if (sizeof($answers)  == sizeof($game_users)  && sizeof($answers) > 0) {

        global $conn;
        $query = "UPDATE game_questions SET 
        `answered` = :answered
        WHERE game_questions.game_id = :game_id AND
         game_questions.question_id = :question_id ";

        try {

            $answered = 1;
            $questions_query = $conn->prepare($query);
            $questions_query->setFetchMode(PDO::FETCH_OBJ);
            $questions_query->bindParam(':answered', $answered);
            $questions_query->bindParam(':game_id', $game_id);
            $questions_query->bindParam(':question_id', $question_id);
            $questions_query->execute();
            $questions_count = $questions_query->rowCount();
            if ($questions_count > 0) {
                $game_question =  $questions_query->fetch();
                $game_question = processGameQuestion($game_question);
            } else {
                $game_question =  null;
            }
            unset($conn);
            return $game_question;
        } catch (PDOException $err) {
            return [];
        };
    } else {
        return false; // not all people havent answered yet
    }
}




function create_answer($answer) {

    if (!empty($answer->text)) {

        global $conn;

        try {
            $query = "INSERT INTO answers   (`user_id`, game_id, question_id, `text`, correct) VALUES   (:user_id, :game_id,  :question_id, :text, :correct)";

            $answer_query = $conn->prepare($query);

            $answer_query->bindParam(':user_id', $answer->user_id);
            $answer_query->bindParam(':game_id', $answer->game_id);
            $answer_query->bindParam(':question_id', $answer->question_id);
            $answer_query->bindParam(':text', $answer->text);
            $answer_query->bindParam(':correct', $answer->correct);
            $answer_query->execute();
            $answer_id = $conn->lastInsertId();
            unset($conn);
            return ($answer_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // answer answer_id was blank
        return false;
    }
}



function processAnswer($answer) {

    $answer->id =  intval($answer->id);
    $answer->user_id =  intval($answer->user_id);
    $answer->game_id =  intval($answer->game_id);
    $answer->question_id =  intval($answer->question_id);
    $answer->score =  intval($answer->score);
    $answer->correct =  (intval($answer->correct)) == 1;
    $answer->finished =  (intval($answer->finished)) == 1;

    return $answer;
}


function processAnswers($answers) {

    foreach ($answers as $answer) {
        processAnswer($answer);
    }

    return $answers;
}
