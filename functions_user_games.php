<?php




function create_user_game($user_game)
{

    global $conn;
    if (!empty($user_game->user_id)) {
        try {
            $query = "INSERT INTO user_games     (user_id, game_id) VALUES   (:user_id, :game_id)";
            $user_game_query = $conn->prepare($query);
            $user_game_query->bindParam(':user_id', $user_game->user_id);
            $user_game_query->bindParam(':game_id', $user_game->game_id);

            $user_game_query->execute();
            $user_game_id = $conn->lastInsertId();
            unset($conn);
            return ($user_game_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // user_game game_id was blank
        return false;
    }
}


function get_user_game($user_game_id = null)
{
    global $conn;
    if ($user_game_id != null) {

        try {
            $query = "SELECT * FROM user_games WHERE user_games.id = :id LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $user_game_id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();

            if ($user_count == 1) {
                $user_game =  $user_query->fetch();
                $user_game =  processUserGame($user_game);
            } else {
                $user_game = null;
            }
            unset($conn);
            return $user_game;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if user id is not greated than 0
        return null;
    }
}




function update_user_game($user_game)
{


    global $conn;
    if ($user_game->id > 0) {

        if ($user_game->ready == 'true' || $user_game->ready == '1' || $user_game->ready == true) {
            $user_game->ready = 1;
        } else {
            $user_game->ready = 0;
        }
        try {
            $query = "UPDATE user_games SET 
            `ready` = :ready
            WHERE id = :id";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':ready', $user_game->ready);
            $user_query->bindParam(':id', $user_game->id);
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



function processUserGame($user_game)
{

    $user_game->id =  intval($user_game->id);
    $user_game->user_id =  intval($user_game->user_id);
    $user_game->game_id =  intval($user_game->game_id);
    $user_game->ready =  (intval($user_game->ready)) == 1;
    return $user_game;
}


function processUserGames($user_games)
{

    foreach ($user_games as $user_game) {
        processUserGame($user_game);
    }

    return $user_games;
}

function getreadies($user_game)
{
    return $user_game->ready;
}

function  setGameAsStarted($game_id)
{
    $all_user_games =  get_user_games_for_game($game_id);

    $readies = array_map('getreadies', $all_user_games);
    $game_ready = true;
    foreach ($readies as $r) {
        if ($r == false) {
            $game_ready = false;
        }
    }



    if ($game_ready) {
        $game = get_game($game_id);
        $game->started = 1;
        update_game($game);
    }
};
