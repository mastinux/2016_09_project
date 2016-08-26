<?php

    function sanitize_string($var) {
        $var = strip_tags($var);
        $var = htmlentities($var);
        $var = stripcslashes($var);
        return $var;
    }

    function connect_to_database() {
        $success = true;
        $err_msg = "";

        $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

        try{
            if ( mysqli_connect_error() )
                throw new Exception("Error during connection to DB.");
        }
        catch(Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success )
            redirect_with_message("index.php", "d", $err_msg);

        return $connection;
    }

    function get_user_balance($username){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select balance from shares_user where email = '$username'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving user balance.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);

        $balance = $row['balance'];

        mysqli_free_result($result);
        mysqli_close($connection);

        return $balance;
    }

    function get_user_shares_amount($username){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select * from shares_order where username='$username' and shares_type='purchase'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving amount of user shares.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $amount = mysqli_num_rows($result);

        mysqli_free_result($result);
        mysqli_close($connection);

        return $amount;
    }

    function get_buying_shares(){
        return get_shares('purchase');
    }

    function get_selling_shares(){
        return get_shares('sales');
    }

    function get_shares($shares_type){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        if ($shares_type)
            if ( $shares_type == 'purchase')
                $sql_statement = "select * from shares where shares_type = '$shares_type' and amount !=0 order by price";
            else
                $sql_statement = "select * from shares where shares_type = '$shares_type' and amount !=0 order by price desc";
        else
            $sql_statement = "select * from shares";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving shares.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        while ($row = mysqli_fetch_assoc($result))
            $rows[] = $row;

        mysqli_free_result($result);
        mysqli_close($connection);

        return $rows;
    }

    function buy_shares($username, $amount){
        $shares_type = "purchase";

        manage_order($username, $shares_type, $amount);
    }

    function sell_shares($username, $amount){
        $shares_type = "sales";

        manage_order($username, $shares_type, $amount);
    }

    function manage_order($username, $shares_type, $amount){
        $interesting_shares = Array();
        $remaining_amount = $amount;

        if ($shares_type == 'purchase')
            $shares = get_buying_shares();
        else
            $shares = get_selling_shares();

        echo "<br><br>";
        foreach ($shares as $s){
            echo $s['shares_type'], " ", $s['amount'], " ", $s['price'], "<br>";
            if ( $remaining_amount <= $s['amount'] ){
                update_shares__insert_shares_order__update_balance($username, $shares_type, $amount, $s['price']);
                break;
            }
            else{

            }
        }

        return 0;
    }

    function update_shares__insert_shares_order__update_balance($username, $shares_type, $amount, $price){
        // TODO execute 3 operation in 1 single connection
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        // update
        $update_sql_statement = "update shares set amount = amount - '$amount' where price='$price'";

        try{
            if ( !mysqli_query($connection, $update_sql_statement) )
                throw new Exception("Problems while updating shares.");
        }catch(Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success )
            redirect_with_message("index.php", "d", $err_msg);

        // TODO continue developing

        //insert
        // insert into shares_order(username, shares_type, amount, price) values('andreapantaleo@gmail.com','sales', 2, 1030);
        $insert_sql_statement = "insert into shares_order(username, shares_type, amount, price) values('$username', '$shares_type', '$amount', '$price')";

        try{
            if ( !mysqli_query($connection, $insert_sql_statement) )
                throw new Exception("Problems while updating shares.");
        }catch(Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success )
            redirect_with_message("index.php", "d", $err_msg);

        mysqli_close($connection);
    }

    function get_non_user_taken_seats($username){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $sql_statement = "select * from theater_booked_seat where username != '$username'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving non user taken seats.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        while ($row = mysqli_fetch_assoc($result))
                $rows[] = $row;

        mysqli_free_result($result);
        mysqli_close($connection);

        return $rows;
    }

    function get_user_taken_seats($username){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        $sql_statement = "select * from theater_booked_seat where username = '$username'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving user taken seats.");
        }catch(Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success )
            redirect_with_message("index.php", "d", $err_msg);

        while ($row = mysqli_fetch_assoc($result))
            $rows[] = $row;

        mysqli_free_result($result);
        mysqli_close($connection);

        return $rows;
    }

    function format_as_json($rows){
        return json_encode($rows);
    }

    function store_to_book_seats($username, $seats){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        try {
            mysqli_autocommit($connection,false);

            foreach ($seats as $s) {
                $row = $s['row'];
                $col = $s['col'];

                if ( $row > ROWS - 1 )
                    throw new Exception("Row index exceeded maximum value.");

                if ( $col > COLUMNS - 1)
                    throw new Exception("Column index exceeded maximum value.");

                $sql_statement = "insert into theater_booked_seat(cln, rwn, username) values('$col','$row','$username')";

                if (!mysqli_query($connection, $sql_statement))
                    throw new Exception("Unable to book selected seats, please try again.");
            }
            if (!mysqli_commit($connection))
                throw new Exception("Commit failed.");
        } catch (Exception $e) {
            mysqli_rollback($connection);
            remove_cookie("toBook");
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function store_to_cancel_seats($username, $seats){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $username = sanitize_string($username);
        $username = mysqli_real_escape_string($connection, $username);

        try {
            mysqli_autocommit($connection,false);

            foreach ($seats as $s) {
                $row = $s['row'];
                $col = $s['col'];

                if ( $row > ROWS - 1 )
                    throw new Exception("Row index exceeded maximum value.");

                if ( $col > COLUMNS - 1)
                    throw new Exception("Column index exceeded maximum value.");

                $sql_statement = "delete from theater_booked_seat where cln='$col' and rwn='$row' and username='$username'";

                if (!mysqli_query($connection, $sql_statement))
                    throw new Exception("Unable to release selected seats, please try again.");
            }
            if ( !mysqli_commit($connection) )
                throw new Exception("Commit failed.");
        }
        catch (Exception $e){
            mysqli_rollback($connection);
            remove_cookie("toCancel");
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
    }

    function check_and_store_to_book_seats($username){
        if ( isset($_COOKIE['toBook']) ){
            $to_book_seats = json_decode($_COOKIE['toBook'], true);
            store_to_book_seats($username, $to_book_seats);
            remove_cookie("toBook");
            redirect_with_message("index.php", "s", "Selected seats have been booked.");
        }
    }

    function check_and_store_to_cancel_seats($username){
        if ( isset($_COOKIE['toCancel']) ){
            $to_cancel_seats = json_decode($_COOKIE['toCancel'], true);
            store_to_cancel_seats($username, $to_cancel_seats);
            remove_cookie("toCancel");
            redirect_with_message("index.php", "s", "Selected booked seats have been canceled.");
        }
    }

?>

