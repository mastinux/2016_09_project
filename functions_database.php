<?php

    // TODO: sanitize input from client, also with database connection

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

    function get_user_shares_amount_by_type($username, $shares_type){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select sum(amount) as amount_sum from shares_order 
                          where username='$username' and shares_type='$shares_type'";

        try{
            if ( !($result = mysqli_query($connection, $sql_statement)) )
                throw new Exception("Problems while retrieving amount of user ".$shares_type." shares.");
        }catch (Exception $e){
            $success = false;
            $err_msg = $e->getMessage();
        }

        if ( !$success)
            redirect_with_message("index.php", "d", $err_msg);

        $row = mysqli_fetch_assoc($result);
        $amount = $row['amount_sum'];

        mysqli_free_result($result);
        mysqli_close($connection);

        if ($amount)
            return $amount;
        else
            return 0;
    }

    function get_user_shares_amount($username){
        return get_user_shares_amount_by_type($username, 'buying') - get_user_shares_amount_by_type($username, 'selling');
    }

    function get_user_ordered_shares($username){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $sql_statement = "select * from shares_order where username = '$username' order by shares_order_id desc";

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

    function get_buying_shares(){
        return get_shares('buying');
    }

    function get_selling_shares(){
        return get_shares('selling');
    }

    function get_shares($shares_type){
        $rows = Array();
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        if ($shares_type)
            if ( $shares_type == 'buying')
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
        $shares_type = "buying";

        manage_order($username, $shares_type, $amount);
    }

    function sell_shares($username, $amount){
        $shares_type = "selling";

        manage_order($username, $shares_type, $amount);
    }

    function manage_order($username, $shares_type, $amount){
        $interesting_shares = Array();
        $remaining_amount = $amount;

        if ($shares_type == 'buying')
            $shares = get_buying_shares();
        else
            $shares = get_selling_shares();

        echo "<br><br>";
        foreach ($shares as $s){
            echo $s['shares_type'], " ", $s['amount'], " ", $s['price'], "<br>";
            if ( $remaining_amount <= $s['amount'] ){
                update_shares__insert_shares_order__update_balance($username, $shares_type, $amount, $s['price']);
                redirect_with_message('index.php', 's', 'Action of '.$shares_type.' shares succeeded.');
                break;
            }
            else{

            }
        }

        return 0;
    }

    function update_shares__insert_shares_order__update_balance($username, $shares_type, $amount, $price){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        try {
            mysqli_autocommit($connection,false);

            // update shares
            $sql_statement = "update shares set amount = (amount - '$amount') 
                                where price = '$price' and shares_type = '$shares_type'";

            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while updating shares.");

            // insert into shares_user
            $sql_statement = "insert into shares_order(username, shares_type, amount, price) 
                              values('$username', '$shares_type', '$amount', '$price')";
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while inserting into shares_order.");

            // sign for balance
            if ( $shares_type == 'selling')
                $sign = "+";
            else
                $sign = "-";

            // update user
            $sql_statement = "update shares_user set balance = (balance $sign ($amount * $price))
                              where email = '$username'";
            if ( !mysqli_query($connection, $sql_statement) )
                throw new Exception("Problems while updating shares_user.".$sql_statement);

            if (!mysqli_commit($connection))
                throw new Exception("Commit failed.");
        } catch (Exception $e) {
            mysqli_rollback($connection);
            $success = false;
            $err_msg = $e->getMessage();
        }

        mysqli_close($connection);

        if( !$success )
            redirect_with_message("index.php", "d", $err_msg);
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

