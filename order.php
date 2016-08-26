<?php
    include 'functions.php';
    include 'functions_database.php';

    session_start();
    if ( $username = user_logged_in() ){
        include 'auth_sessions.php';
        set_https();
    }
    else{
        redirect_with_message('index.php', 'w', 'You must be logged in to buy or sell shares.');
    }

    check_enabled_cookies();

    $success = true;
    $err_msg = "";

    switch($_SERVER['REQUEST_METHOD']) {
        case 'GET': {
            redirect_with_message("index.php", "w", "Buy or sell action must be a post method.");
            break;
        }
        case 'POST': {
            if ( !isset($_POST['amount']) || !isset($_POST['type']) )
                redirect_with_message("index.php", "w", "Amount not set in buy or sell form.");
            $amount = $_POST['amount'];
            $type = $_POST['type'];
            break;
        }
    }

    switch ($type){
        case "Buy":{
            echo "<br>you want to buy ", $amount, " shares";
            buy_shares($username, $amount);
            break;
        }
        case "Sell":{
            echo "<br>you want to sell ", $amount, " shares";
            sell_shares($username, $amount);
            break;
        }
        default:{
            redirect_with_message("index.php", "w", "Type of action not valid set in form.");
        }
    }

?>