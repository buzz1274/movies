<script type="text/template" id="tpl-movie-list-item">
    <td>
        <a href='http://www.imdb.com/title/<%= Movie.imdb_id %>/'
           target='_blank'><%= Movie.title %></a>
        <% if(Movie.favourite) { %>
            <i class="icon-star" />
        <% } %>
    </td>
    <td class='centre'><%= Movie.release_year %></td>
    <td class='centre'><%= Movie.imdb_rating %></td>
    <td><%= Movie.runtime %></td>
    <td class='centre'>
        <% if(Movie.certificate) { %>
            <img class="centre"
                 src='/assets/img/<%= Movie.certificate %>.png' />
        <% } else { %>
            <div class="centre">-</div>
        <% } %>
    </td>
    <td class='centre'><%= Movie.date_added %></td>
    <td class='centre'>
        <% if(Movie.hd) { %>
            <img class="centre tick_cross" src="/assets/img/tick.png">
        <% } else { %>
            <img class="centre tick_cross" src="/assets/img/cross.png">
        <% } %>
    </td>
    <% if (user.authenticated) {%>
        <td class='centre'>
            <% if(Movie.watched) { %>
                <img class="centre tick_cross" src="/assets/img/tick.png">
            <% } else { %>
                <img class="centre tick_cross" src="/assets/img/cross.png">
            <% } %>
        </td>
    <% } %>
    <td>
        <div class="btn-group">
            <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown"
               href="#">
                Actions <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">
                <li class="detail_link">
                    <a class="menu_item">
                        <i class="icon-search"></i>
                        &nbsp;Details
                    </a>
                </li>
                <% if (user.authenticated) {%>
                    <li class="favourite_link">
                        <% if(!Movie.favourite) { %>
                            <a id="favourites_link" class="menu_item">
                                <i class="icon-star"></i>
                                &nbsp;Add to Favourites
                            </a>
                        <% } else { %>
                            <a id="favourites_link" class="menu_item">
                                <i class="icon-star"></i>
                                &nbsp;Remove from Favourites
                            </a>
                        <% } %>
                    </li>
                    <li class="queue_download_link">
                        <a class="menu_item">
                            <i class="icon-download"></i>
                            &nbsp;Queue for Download
                        </a>
                    </li>
                <% } %>
            </ul>
        </div>
    </td>
</script>