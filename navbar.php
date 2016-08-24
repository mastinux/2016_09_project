<nav class="navbar navbar-default" id="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <!--
                uncomment on server
                <a class="navbar-brand" href="/~s231826/c0e1ee/">
            -->
            <a class="navbar-brand" href="/2016_09_project">
                Shares
            </a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <?php
                    if ( !user_logged_in() ) {
                        echo "<li>
                                <form method=\"get\" action=\"auth_login.php\" class=\"navbar-form navbar-left\">
                                  <a href=\"auth_login.php\">                                  
                                    <button type=\"button\" class=\"btn btn-default\">
                                        <span class=\"glyphicon glyphicon-log-in\" aria-hidden=\"true\"></span> Login
                                    </button>
                                   </a>
                                </form>
                              </li>";
                    }
                    else{
                        echo "
                              <li>
                                  <p class=\"navbar-text\">Signed in as <b>".$username."</b></p>
                              </li>
                              <li>
                                  <form class=\"navbar-form navbar-left\">
                                      <a href=\"auth_logout.php\">
                                        <button type=\"button\" class=\"btn btn-default\">
                                            <span class=\"glyphicon glyphicon-log-out\" aria-hidden=\"true\"></span>
                                            Logout                                                
                                        </button>
                                      </a>
                                  </form>
                              </li>";
                    }
                ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>