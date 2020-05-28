<?php



function create_question($question)
{

    global $conn;
    if (!empty($question)) {


        try {
            $query = "INSERT INTO questions (game_type, text) VALUES   (:game_type, :text)";
            $question_query = $conn->prepare($query);
            $question_query->bindParam(':game_type', $question->game_type);
            $question_query->bindParam(':text', $question->text);
            $question_query->execute();
            $question_id = $conn->lastInsertId();
            unset($conn);
            return ($question_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // question question_id was blank
        return false;
    }
}
