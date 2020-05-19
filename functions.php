<?php



function updated_at_string()
{
    return  date('Y-m-d H:i:s');
}


function get_random_hex($num_bytes = 4, $prefix = '')
{
    return $prefix . bin2hex(openssl_random_pseudo_bytes($num_bytes));
}



include('functions_users.php');
include('functions_games.php');
include('functions_user_games.php');
include('functions_answers.php');
include('functions_votes.php');
