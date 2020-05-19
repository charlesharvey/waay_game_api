<?php

ini_set('default_charset', 'UTF-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header('Content-Type: application/json;charset=UTF-8');



include('connect.php');
include('functions.php');



if (isset($_GET['route'])) {
    $route = $_GET['route'];

    if ($route == 'users') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/users/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                include('routes/users/update.php');
            } else {
                include('routes/users/show.php');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/users/create.php');
            } else {
                include('routes/users/index.php');
            }
        }
    } // end of if route is projects


    if ($route == 'games') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/games/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                include('routes/games/update.php');
            } else {
                include('routes/games/show.php');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/games/create.php');
            } else {
                include('routes/games/index.php');
            }
        }
    } // end of if route is tasks



    if ($route == 'votes') {
        if (isset($_GET['id'])) {
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/votes/create.php');
            }
        }
    } // end of if route is votes



    if ($route == 'answers') {
        if (isset($_GET['id'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                include('routes/answers/delete.php');
            } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                include('routes/answers/update.php');
            } else {
                include('routes/answers/show.php');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/answers/create.php');
            } else {
                include('routes/answers/index.php');
            }
        }
    } // end of if route is answers

    if ($route == 'user_games') {
        if (isset($_GET['id'])) {

            if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                include('routes/user_games/update.php');
            }
        } else {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                include('routes/user_games/create.php');
            }
        }
    } // end of if route is user_games




} else {



    //  error
    http_response_code(404);
    echo json_encode('error no route');
}
