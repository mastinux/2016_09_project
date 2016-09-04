<?php
    include 'functions.php';
    include 'functions_database.php';
    include 'functions_messages.php';

    session_start();
    if ( $username = user_logged_in() ){
        include 'auth_sessions.php';
        set_https();
    }
    else{
        unset_https();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shares Manager</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="bootstrap/html5shiv.min.js"></script>
    <script type="text/javascript" src="bootstrap/respond.min.js"></script>
    <![endif]-->

    <link href="shares_style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="general_functions.js"></script>
    <script type="text/javascript" src="shares_functions.js"></script>
</head>

<body>

    <?php
        include 'navbar.php';
        include 'no_script_messages.html';
        manage_messages();
    ?>

<div class="col-lg-4" id="left-panel">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?php if ($username){?>
                    Your Account
                <?php }else{?>
                    Log in or Register
                <?php }?>
            </div>
            <div class="panel-body">
                <?php
                    if ( !$username ) {
                ?>
                    <form method="get" action="auth_login.php" class="navbar-form navbar-left">
                        <a href="auth_login.php">
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> Login
                            </button>
                        </a>
                    </form>
                <?php
                    }
                    else{
                ?>
                    <form class="navbar-form navbar-left">
                        <a href="auth_logout.php">
                            <button type="button" class="btn btn-default">
                                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                                Logout
                            </button>
                        </a>
                    </form>
                <?php
                    }
                ?>
            </div>

            <?php if ($username) {?>
            <ul class="list-group">
                <li class="list-group-item">
                    Username: <?php echo $username;?>
                </li>
                <li class="list-group-item">
                    Balance: <?php echo get_user_balance($username); ?>
                </li>
                <li class="list-group-item">
                    Amount of shares: <?php echo get_user_shares_amount($username); ?>
                </li>
                <li class="list-group-item">
                    <?php
                    $shares = get_user_ordered_shares($username);
                    if (count($shares) > 0){
                        ?>
                        <div class="panel panel-success">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Your past orders</h3>
                                </div>

                                <table class="table">
                                    <tr>
                                        <th>Order no.</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Price</th>
                                    </tr>
                                    <?php
                                    foreach ($shares as $s){
                                        echo "<tr>";
                                        echo "<td>".$s['shares_order_id']."</td>";
                                        if ($s['shares_type'] == 'offer')
                                            echo "<td>purchase</td>";
                                        else
                                            echo "<td>sale</td>";
                                        echo "<td>".$s['amount']."</td>";
                                        echo "<td>".$s['price']."</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </table>
                            </div>
                    <?php } ?>
                </li>
            </ul>
            <?php }?>

        </div>
        </div>
    </div>

    <div class="col-lg-8" id="right-panels">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Shares book</h3>
            </div>
            <table class="table">
                <tr>
                    <th>Demand amount</th>
                    <th>Demand price</th>
                    <th>Offer price</th>
                    <th>Offer amount</th>
                </tr>
                <?php
                $demand_shares = get_demand_shares();
                if ( count($demand_shares) > TABLE_ROWS)
                    array_slice($demand_shares, 0, 5);
                $demand_dimension = count($demand_shares);
                $offer_shares = get_offer_shares();
                if ( count($offer_shares) > TABLE_ROWS)
                    array_slice($offer_shares, 0, 5);
                $offer_dimension = count($offer_shares);

                for ($i = 0; $i < TABLE_ROWS; $i++){
                    echo "<tr>";
                    if ($i < $demand_dimension){
                        echo "<td>".$demand_shares[$i]['amount']."</td>";
                        echo "<td>".$demand_shares[$i]['price']."</td>";
                    }
                    else
                        echo "<td></td><td></td>";
                    if ($i < $offer_dimension){
                        echo "<td>".$offer_shares[$i]['amount']."</td>";
                        echo "<td>".$offer_shares[$i]['price']."</td>";
                    }
                    else
                        echo "<td></td><td></td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
        <?php if($username){ ?>
            <div class="panel panel-warning">
            <div class="panel-heading">
                <h3 class="panel-title">Order</h3>
            </div>
            <div class="panel-body">
                <div class="input-group col-lg-12">
                    <form method="post" action="order.php" onsubmit="return checkAmount();">
                        <div class="input-group">
                            <span class="input-group-addon">Amount</span>
                            <input type="number" min="0" step="1" value="0" id="amount-of-shares"
                                   name="amount" class="form-control" aria-describedby="basic-addon1">
                        </div>
                        <div class="btn-group btn-group-justified" role="group" aria-label="...">
                            <div class="btn-group" role="group">
                                <input type="submit" name="type" class="btn btn-default" value="Sell"/>
                            </div>
                            <div class="btn-group" role="group">
                                <input type="submit" name="type" class="btn btn-default" value="Buy"/>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <script type="text/javascript">
        if (navigator.cookieEnabled == false) {
            // preventing site usage
            removeElementById('left-panel');
            removeElementById('right-panels');
            printCookieDisabledMessage();
        }
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="bootstrap/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>