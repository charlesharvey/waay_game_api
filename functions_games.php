<?php

function get_game($game_id = null)
{
    global $conn;
    if ($game_id != null) {

        try {
            $query = "SELECT * FROM games WHERE games.id = :id LIMIT 1";
            $game_query = $conn->prepare($query);
            $game_query->bindParam(':id', $game_id);
            $game_query->setFetchMode(PDO::FETCH_OBJ);
            $game_query->execute();

            $game_count = $game_query->rowCount();

            if ($game_count == 1) {
                $game =  $game_query->fetch();
                $game =  processGame($game);
            } else {
                $game = null;
            }
            unset($conn);
            return $game;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if game id is not greated than 0
        return null;
    }
}

function get_game_from_code($game_code = null)
{
    global $conn;
    if ($game_code != null) {

        try {
            $query = "SELECT * FROM games WHERE games.code = :code LIMIT 1";
            $game_query = $conn->prepare($query);
            $game_query->bindParam(':code', $game_code);
            $game_query->setFetchMode(PDO::FETCH_OBJ);
            $game_query->execute();

            $game_count = $game_query->rowCount();

            if ($game_count == 1) {
                $game =  $game_query->fetch();
                $game =  processGame($game);
            } else {
                $game = null;
            }
            unset($conn);
            return $game;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if game id is not greated than 0
        return null;
    }
}

function get_random_questions($number)
{
    global $conn;

    try {
        $query = "SELECT * FROM questions ORDER BY RAND() LIMIT $number";
        $question_query = $conn->prepare($query);
        $question_query->setFetchMode(PDO::FETCH_OBJ);
        $question_query->execute();
        $question_count = $question_query->rowCount();


        if ($question_count > 0) {
            $questions =  $question_query->fetchAll();
            foreach ($questions as $question) {
                $question->finished = 0;
                $question->answered = 0;
                $question->voted = 0;
            }
            $questions = processQuestions($questions);
        } else {
            $questions =  [];
        }

        unset($conn);
        return $questions;
    } catch (PDOException $err) {
        return null;
    };
}



function create_game($game)
{

    global $conn;
    if (!empty($game->user_code)) {


        $user = get_user_from_code($game->user_code);
        $game_type = 1;
        $rounds_to_play = $game->rounds_to_play;
        $code = get_random_hex(3, 'g');

        try {
            $query = "INSERT INTO games   (user_id, game_type, rounds_to_play, code) VALUES   (:user_id, :game_type, :rounds_to_play, :code)";
            $game_query = $conn->prepare($query);
            $game_query->bindParam(':user_id', $user->id);
            $game_query->bindParam(':game_type', $game_type);
            $game_query->bindParam(':rounds_to_play', $rounds_to_play);
            $game_query->bindParam(':code', $code);


            $game_query->execute();
            $game_id = $conn->lastInsertId();
            unset($conn);
            return ($game_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // game game_id was blank
        return false;
    }
}


function update_game($game)
{

    global $conn;
    if ($game->id > 0) {
        try {
            $query = "UPDATE games SET 
            `started` = :started
            WHERE id = :id";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':started', $game->started);
            $user_query->bindParam(':id', $game->id);
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


function processGame($game)
{

    $game->id =  intval($game->id);
    $game->user_id =  intval($game->user_id);
    $game->finished =  (intval($game->finished)) == 1;
    $game->started =  (intval($game->started)) == 1;
    return $game;
}

function processGameQuestion($game_question)
{

    $game_question->id =  intval($game_question->id);
    $game_question->question_id =  intval($game_question->question_id);
    $game_question->game_id =  intval($game_question->game_id);
    $game_question->finished =  (intval($game_question->finished)) == 1;
    $game_question->answered =  (intval($game_question->answered)) == 1;
    $game_question->voted =  (intval($game_question->voted)) == 1;
    return $game_question;
}




function processGames($games)
{

    foreach ($games as $game) {
        processGame($game);
    }

    return $games;
}



function processQuestion($question)
{

    $question->id =  intval($question->id);
    // if (!empty($question->finished)) {
    $question->finished =  (intval($question->finished)) == 1;
    // }
    // if (!empty($question->answered)) {
    $question->answered =  (intval($question->answered)) == 1;
    // }

    $question->voted =  (intval($question->voted)) == 1;
    return $question;
}




function processQuestions($questions)
{

    foreach ($questions as $question) {
        processQuestion($question);
    }



    return $questions;
}
function processGameQuestions($questions)
{

    foreach ($questions as $question) {
        processGameQuestion($question);
    }

    return $questions;
}




function create_game_user($game_id, $user_id)
{
    global $conn;
    if ($game_id > 0 && $user_id > 0) {
        try {
            $query = "INSERT INTO user_games   (game_id, user_id) VALUES   (:game_id, :user_id)";
            $game_query = $conn->prepare($query);
            $game_query->bindParam(':game_id', $game_id);
            $game_query->bindParam(':user_id', $user_id);
            $game_query->execute();
            $game_id = $conn->lastInsertId();
            unset($conn);
            return ($game_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // game game_id was blank
        return false;
    }
}


function setGameQuestionAsVoted($game_question)
{

    global $conn;
    $query = "UPDATE game_questions SET 
        `voted` = :voted
        WHERE game_questions.game_id = :game_id AND
         game_questions.question_id = :question_id ";

    try {
        $voted = 1;
        $questions_query = $conn->prepare($query);
        $questions_query->setFetchMode(PDO::FETCH_OBJ);
        $questions_query->bindParam(':voted', $voted);
        $questions_query->bindParam(':game_id', $game_question->game_id);
        $questions_query->bindParam(':question_id', $game_question->question_id);
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
}

function create_game_questions($game_id, $number)
{
    if ($game_id > 0) {

        global $conn;
        try {
            $questions = get_random_questions($number);
            foreach ($questions as $question) {

                $query = "INSERT INTO game_questions   (game_id, question_id) VALUES   (:game_id, :question_id)";
                $game_query = $conn->prepare($query);
                $game_query->bindParam(':game_id', $game_id);
                $game_query->bindParam(':question_id', $question->id);
                $game_query->execute();
                $game_q_id = $conn->lastInsertId();
            }; // end questions loop
            unset($conn);
            return true;
        } catch (PDOException $err) {
            unset($conn);
            return false;
        };
    } else { // game game_id was blank
        return false;
    }
}



function get_questions_for_game($game_id)
{
    global $conn;


    $query = "SELECT questions.*, finished, answered, voted  FROM questions LEFT JOIN game_questions ON game_questions.question_id = questions.id WHERE game_questions.game_id = :game_id ORDER BY  questions.created_at DESC";

    try {

        $questions_query = $conn->prepare($query);
        $questions_query->setFetchMode(PDO::FETCH_OBJ);
        $questions_query->bindParam(':game_id', $game_id);
        $questions_query->execute();
        $questions_count = $questions_query->rowCount();

        if ($questions_count > 0) {
            $questions =  $questions_query->fetchAll();
            $questions = processQuestions($questions);
        } else {
            $questions =  [];
        }

        unset($conn);
        return $questions;
    } catch (PDOException $err) {
        return [];
    };
}

function get_game_questions_for_game($game_id)
{
    global $conn;
    $query = "SELECT *  FROM game_questions WHERE game_questions.game_id = :game_id ORDER BY  game_questions.id ASC";

    try {

        $questions_query = $conn->prepare($query);
        $questions_query->setFetchMode(PDO::FETCH_OBJ);
        $questions_query->bindParam(':game_id', $game_id);
        $questions_query->execute();
        $questions_count = $questions_query->rowCount();
        if ($questions_count > 0) {
            $game_questions =  $questions_query->fetchAll();
            $game_questions = processGameQuestions($game_questions);
        } else {
            $game_questions =  [];
        }
        unset($conn);
        return $game_questions;
    } catch (PDOException $err) {
        return [];
    };
}


function get_game_question($game_id, $question_id)
{
    global $conn;
    $query = "SELECT *  FROM game_questions WHERE game_questions.game_id = :game_id AND game_questions.question_id = :question_id  LIMIT 1";
    try {

        $questions_query = $conn->prepare($query);
        $questions_query->setFetchMode(PDO::FETCH_OBJ);
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
}





function get_user_games_for_game($game_id)
{
    global $conn;
    $query = "SELECT user_games.*, code FROM user_games 
    LEFT JOIN users ON users.id = user_games.user_id
    WHERE user_games.game_id = :game_id ORDER BY  user_games.id ASC";

    try {

        $questions_query = $conn->prepare($query);
        $questions_query->setFetchMode(PDO::FETCH_OBJ);
        $questions_query->bindParam(':game_id', $game_id);
        $questions_query->execute();
        $questions_count = $questions_query->rowCount();
        if ($questions_count > 0) {
            $game_questions =  $questions_query->fetchAll();
            $game_questions = processUserGames($game_questions);
        } else {
            $game_questions =  [];
        }
        unset($conn);
        return $game_questions;
    } catch (PDOException $err) {
        return [];
    };
}




function get_users_for_game($game_id)
{
    global $conn;


    $query = "SELECT users.*, user_games.ready  FROM users LEFT JOIN user_games ON user_games.user_id = users.id WHERE user_games.game_id = :game_id ORDER BY  users.created_at DESC";

    try {

        $users_query = $conn->prepare($query);
        $users_query->setFetchMode(PDO::FETCH_OBJ);
        $users_query->bindParam(':game_id', $game_id);
        $users_query->execute();
        $users_count = $users_query->rowCount();

        if ($users_count > 0) {
            $users =  $users_query->fetchAll();
            $users = processUsers($users);
        } else {
            $users =  [];
        }

        unset($conn);
        return $users;
    } catch (PDOException $err) {
        return [];
    };
}
