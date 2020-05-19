<?php


function get_vote($vote_id = null)
{
    global $conn;
    if ($vote_id != null) {

        try {
            $query = "SELECT * FROM votes WHERE votes.id = :id LIMIT 1";
            $vote_query = $conn->prepare($query);
            $vote_query->bindParam(':id', $vote_id);
            $vote_query->setFetchMode(PDO::FETCH_OBJ);
            $vote_query->execute();

            $vote_count = $vote_query->rowCount();

            if ($vote_count == 1) {
                $vote =  $vote_query->fetch();
                $vote =  processVote($vote);
            } else {
                $vote = null;
            }
            unset($conn);
            return $vote;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if vote id is not greated than 0
        return null;
    }
}

function get_votes_for_answer($answer_id = null)
{
    global $conn;
    if ($answer_id != null) {

        try {
            $query = "SELECT votes.* FROM votes  LEFT JOIN answers on answers.id=votes.answer_id
            WHERE votes.answer_id = :answer_id";
            $votes_query = $conn->prepare($query);
            $votes_query->bindParam(':answer_id', $answer_id);
            $votes_query->setFetchMode(PDO::FETCH_OBJ);
            $votes_query->execute();

            $votes_count = $votes_query->rowCount();

            if ($votes_count > 0) {
                $votes =  $votes_query->fetchAll();
                $votes = processVotes($votes);
            } else {
                $votes =  [];
            }
            unset($conn);
            return $votes;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if vote id is not greated than 0
        return null;
    }
}



function get_votes_for_game($game_id = null)
{
    global $conn;
    if ($game_id != null) {

        try {
            $query = "SELECT votes.* FROM votes WHERE votes.game_id = :game_id";
            $votes_query = $conn->prepare($query);
            $votes_query->bindParam(':game_id', $game_id);
            $votes_query->setFetchMode(PDO::FETCH_OBJ);
            $votes_query->execute();

            $votes_count = $votes_query->rowCount();

            if ($votes_count > 0) {
                $votes =  $votes_query->fetchAll();
                $votes = processVotes($votes);
            } else {
                $votes =  [];
            }
            unset($conn);
            return $votes;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if vote id is not greated than 0
        return null;
    }
}

function setAnswerScoreFromVotes($vote)
{
    global $conn;

    if ($vote->answer_id) {



        $score = 0;


        // $score = $vote->score; 
        // this is giving the point to the person who wrote the correct answer
        // not the person who guessed for the correct answer;




        $answer = get_answer($vote->answer_id);

        // var_dump($answer);
        if ($answer->correct != 1) {
            // THEY BAMBOOZLED
            $votes_for_answer = get_votes_for_answer($answer->id);
            //  2 points for each bamboozle
            $score += (sizeof($votes_for_answer) * 2);
        }

        try {
            $query = "UPDATE answers SET `score` = :score WHERE id = :answer_id ";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':score', $score);
            $user_query->bindParam(':answer_id', $vote->answer_id);
            $user_query->execute();
            unset($conn);
            return true;
        } catch (PDOException $err) {
            return false;
        };
    } else { // vote score was blank
        return false;
    }
}


function get_votes_for_game_question($game_question_id)
{
    global $conn;

    $query = "SELECT votes.*,  answers.correct, answers.user_id as auid   FROM votes  
    LEFT JOIN answers ON answers.id = votes.answer_id
     WHERE game_question_id = :game_question_id  ORDER BY  votes.id DESC";

    try {
        $votes_query = $conn->prepare($query);
        $votes_query->setFetchMode(PDO::FETCH_OBJ);
        $votes_query->bindParam(':game_question_id', $game_question_id);
        $votes_query->execute();
        $votes_count = $votes_query->rowCount();

        if ($votes_count > 0) {
            $votes =  $votes_query->fetchAll();
            $votes = processVotes($votes);
        } else {
            $votes =  [];
        }

        unset($conn);
        return $votes;
    } catch (PDOException $err) {
        return [];
    };
}


function create_vote($vote)
{

    if (!empty($vote->answer_id)) {

        global $conn;



        $score = $vote->score;

        try {
            $query = "INSERT INTO votes   (`user_id`, game_id, answer_id, game_question_id, score) VALUES   (:user_id, :game_id,  :answer_id, :game_question_id, :score)";

            $vote_query = $conn->prepare($query);

            $vote_query->bindParam(':user_id', $vote->user_id);
            $vote_query->bindParam(':game_id', $vote->game_id);
            $vote_query->bindParam(':game_question_id', $vote->game_question_id);
            $vote_query->bindParam(':answer_id', $vote->answer_id);
            $vote_query->bindParam(':score', $score);
            $vote_query->execute();
            $vote_id = $conn->lastInsertId();
            unset($conn);
            return ($vote_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // vote vote_id was blank
        return false;
    }
}



function processVote($vote)
{

    $vote->id =  intval($vote->id);
    $vote->user_id =  intval($vote->user_id);
    $vote->game_id =  intval($vote->game_id);
    $vote->answer_id =  intval($vote->answer_id);
    $vote->score =  intval($vote->score);

    return $vote;
}


function processVotes($votes)
{

    foreach ($votes as $vote) {
        processVote($vote);
    }

    return $votes;
}
