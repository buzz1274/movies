<script type="text/template" id="tpl-movie-downloaded">
    <td><a href='/#id=<%= movie_id %>'><%= title %></a></td>
    <td class='centre'><%= filesize %></td>
    <td class='centre'><%= date_downloaded %></td>
    <td class='centre'><%= status %></td>
    <td>
        <div class="btn-group">
            <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown" href="#">
                Actions <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">
                <% if (user.authenticated) {%>
                    <li class="download_link">
                        <a class="menu_item">
                            <i class="icon-download"></i>
                            &nbsp;Download
                        </a>
                    </li>
                    <li class="favourite_link">
                        <% if(status != 'Complete' && status != 'Cancelled') { %>
                            <a id="cancel_download_link" class="menu_item">
                                <i class="icon-trash"></i>
                                &nbsp;Cancel Download
                            </a>
                        <% } %>
                    </li>
                <% } %>
            </ul>
        </div>
    </td>
</script>