<script type="text/template" id="tpl-navbar">
    <div class="navbar-inner">
        <div class="container-fluid">
            <div id="auth" class="container-fluid pull-right">
                <% if (!authenticated) {%>
                    <span id="login_popup_link" class="auth_link">Login</span>
                <% } else { %>
                    <span id="authenticated">
                        Welcome Back&nbsp;<%= name %>,
                        <span id="logout_link" class="auth_link">Logout</span>
                    </span>
                <% } %>
            </div>
            <a class="brand" href="/#">movieDB</a>
            <span class="version">v0.6</span>
        </div>
    </div>
    <div id="navbar-menu" style="">
        <div class="menu-header container-fluid" style="">
            <div class="btn-group">
                <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
                    Tools
                </a>
                <ul class="dropdown-menu pull-left">
                    <li>
                        <a class="menu_item" href="#">
                            &nbsp;Movies
                        </a>
                    </li>
                    <li>
                        <a class="menu_item" href="#download-queue">
                            &nbsp;Download Queue
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div id="login_popup"></div>
</script>