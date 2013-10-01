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
    <div style="background-color:#000000;line-height:30px;color:#FFFFFF;margin-top:-1px;">
        <div class="menu-header container-fluid" style="">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#" style="color:#FFFFFF;">
                Tools
            </a>
            <ul class="dropdown-menu">
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
    <div id="login_popup"></div>
</script>