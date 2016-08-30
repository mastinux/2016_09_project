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

    function get_user_shares_amount_by_type($username, $shares_type){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        $shares_type = sanitize_string($shares_type);
        $shares_type = mysqli_real_escape_string($connection, $shares_type);

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

    function get_buying_shares(){
        return get_shares('buying');
    }

    function get_selling_shares(){
        return get_shares('selling');
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
        $order_cost = 0;

        $amount = sanitize_string($amount);

        if ($shares_type == 'buying') {
            $shares = get_buying_shares();
        }
        else {
            $user_shares_amount = get_user_shares_amount($username);
            if ( $user_shares_amount == 0 || $user_shares_amount < $amount)
                redirect_with_message('index.php', 'w', 'You have '.$user_shares_amount.' shares, to sell '.$amount.' shares you have to buy more of them.');
            $shares = get_selling_shares();
        }

        foreach ($shares as $s){
            if ( $remaining_amount <= $s['amount'] ){
                $s['amount'] = $remaining_amount;
                $remaining_amount = 0;
                $interesting_shares[] = $s;
                $order_cost += $s['amount'] * $s['price'];
                break;
            }
            else{
                $remaining_amount -= $s['amount'];
                $interesting_shares[] = $s;
                $order_cost += $s['amount'] * $s['price'];
            }
        }

        /*
        echo $username, "<br>";
        echo "balance: ", get_user_balance($username), "<br>";
        echo "cost: ", $order_cost, "<br><br>";
        foreach ($interesting_shares as $interesting_share) {
            echo $interesting_share['amount'], " ", $interesting_share['price'],"<br>";
        }
        echo "<br>";
        */

        if ( $shares_type == 'buying' && get_user_balance($username) < $order_cost )
            redirect_with_message('index.php', 'w', 'You have not enough money ('.$order_cost.') to buy '.$amount.' shares. Please reduce the amount or sell some of your shares.');

        if ($remaining_amount != 0)
            redirect_with_message('index.php', 'w', 'Sorry, there are not '.$amount.' shares available for '.$shares_type.' action.');

        update_shares__insert_shares_order__update_balance($username, $shares_type, $interesting_shares);
        redirect_with_message('index.php', 's', 'Action of '.$shares_type.' '.$amount.' shares succeeded.');
    }

    function update_shares__insert_shares_order__update_balance($username, $shares_type, $shares){
        $success = true;
        $err_msg = "";

        $connection = connect_to_database();

        try {
            mysqli_autocommit($connection,false);

            foreach ($shares as $s){
                $amount = $s['amount'];
                $price = $s['price'];

                // update shares
                $sql_statement = "update shares set amount = (amount - $amount) 
                                where price = $price and shares_type = '$shares_type'";
                if ( !mysqli_query($connection, $sql_statement) )
                    throw new Exception("Problems while updating shares.");

                // insert into shares_user
                $sql_statement = "insert into shares_order(username, shares_type, amount, price) 
                              values('$username', '$shares_type', $amount, $price)";
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
            }

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

?>

