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
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Theater Booker</title>
    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="bootstrap/html5shiv.min.js"></script>
    <script type="text/javascript" src="bootstrap/respond.min.js"></script>
    <![endif]-->

    <link href="shares_style.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="z_theater_map_functions.js"></script>
</head>

<body>
    <noscript>
        <div class="no-script-warning">
            Sorry: Your browser does not support or has disabled javascript.
        </div>
        <br>
        <div class="no-script-info">
            Please use a different browser or enabled javascript.
        </div>
        <br>
    </noscript>

    <?php include 'navbar.php'; ?>

    <?php
        manage_messages();

        $line = Array(-1, -1, -1, -1);
        $table_lines = Array();
        for ( $i = 0; $i < TABLE_ROWS; $i++)
            $table_lines[$i] = $line;
    ?>

    <div class="col-lg-12">
        <div class="panel panel-default">
            <!-- Default panel contents -->
            <div class="panel-heading">
                <h3 class="panel-title">Shares book</h3>
            </div>
            <!--
                <div class="panel-body">
                    <p>...</p>
                </div>
            -->

            <!-- Table -->
            <table class="table">
                <tr>
                    <th>Amount of purchase</th>
                    <th>Price of purchase</th>
                    <th>Price of sales</th>
                    <th>Amount of sales</th>
                </tr>
                <?php
                    foreach ($table_lines as $line)
                        if ($line[0] == -1)
                            echo "<tr><td></td><td></td><td></td><td></td></tr>";
                        else
                            echo "
                                <tr>
                                    <td>$line[0]</td>
                                    <td>$line[1]</td>
                                    <td>$line[2]</td>
                                    <td>$line[3]</td>
                                </tr>";
                ?>
            </table>
        </div>
    </div>

    <?php if ($username) { ?>
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Your account</h3>
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            Username: <?php echo $username;?>
                        </li>
                        <li class="list-group-item">
                            Balance: <?php echo get_user_balance($username); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Order</h3>
                </div>
                <div class="panel-body">
                    <input type="number" min="0" step="1" value="0">
                </div>
            </div>
        </div>

    <?php } ?>
    <script type="text/javascript">
        if (navigator.cookieEnabled == true) {

        }
        else{
            // preventing site usage
            printCookieDisabledMessage();
        }
    </script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="bootstrap/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
</body>
</html>