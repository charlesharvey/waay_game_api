<?php


function get_users()
{
    global $conn;


    $query = "SELECT *  FROM users ORDER BY  users.created_at DESC";

    try {

        $users_query = $conn->prepare($query);
        $users_query->setFetchMode(PDO::FETCH_OBJ);
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



function get_user($user_id = null)
{
    global $conn;
    if ($user_id != null) {

        try {
            $query = "SELECT * FROM users WHERE users.id = :id LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $user_id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();

            if ($user_count == 1) {
                $user =  $user_query->fetch();
                $user =  processUser($user);
            } else {
                $user = null;
            }
            unset($conn);
            return $user;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if user id is not greated than 0
        return null;
    }
}

function get_user_from_code($user_code = null)
{
    global $conn;
    if ($user_code != null) {

        try {
            $query = "SELECT * FROM users WHERE users.code = :code LIMIT 1";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':code', $user_code);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();

            $user_count = $user_query->rowCount();

            if ($user_count == 1) {
                $user =  $user_query->fetch();
                $user =  processUser($user);
            } else {
                $user = null;
            }
            unset($conn);
            return $user;
        } catch (PDOException $err) {
            return null;
        };
    } else { // if user id is not greated than 0
        return null;
    }
}



function create_user($user)
{

    global $conn;
    if (!empty($user->nickname)) {


        $code = get_random_hex(10);

        try {
            $query = "INSERT INTO users     (nickname, code) VALUES   (:nickname, :code)";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':nickname', $user->nickname);
            $user_query->bindParam(':code', $code);


            $user_query->execute();
            $user_id = $conn->lastInsertId();
            unset($conn);
            return ($user_id);
        } catch (PDOException $err) {

            return false;
        };
    } else { // user game_id was blank
        return false;
    }
}




function update_user($user_id, $user)
{
    global $conn;
    if ($user_id > 0) {
        try {


            $updated_at =   updated_at_string();
            $query = "UPDATE users SET 
            `nickname` = :nickname, 
            `updated_at` = :updated_at,
            WHERE id = :id";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':nickname', $user->nickname);
            $user_query->bindParam(':updated_at', $updated_at);
            $user_query->bindParam(':id', $user_id);
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




function delete_user($user_id)
{

    global $conn;
    if ($user_id > 0) {

        try {
            $query = "DELETE FROM users  WHERE id = :id    ";
            $user_query = $conn->prepare($query);
            $user_query->bindParam(':id', $user_id);
            $user_query->setFetchMode(PDO::FETCH_OBJ);
            $user_query->execute();
            unset($conn);
            return true;
        } catch (PDOException $err) {
            return false;
        };
    } else {
        return false;
    }
}


function processUser($user)
{

    $user->id = intval($user->id);
    if (isset($user->ready)) {
        $user->ready = (intval($user->ready)) == 1;
    }

    return $user;
}


function processUsers($users)
{

    foreach ($users as $user) {
        processUser($user);
    }

    return $users;
}
