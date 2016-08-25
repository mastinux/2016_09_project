<?php
    /* TODO: develop page */
    echo "order page";

    include 'functions.php';
    include 'functions_database.php';

    set_https();
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

    echo "<br><br>", $amount, " ",$type;
?>