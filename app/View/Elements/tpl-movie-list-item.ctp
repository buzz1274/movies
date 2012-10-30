<script type="text/template" id="tpl-movie-list-item">
    <td>
        <a href='http://www.imdb.com/title/<%= Movie.imdb_id %>/'
           target='_blank'>
            <%= Movie.title %>
        </a>
    </td>
    <td class='centre'><%= Movie.release_year %></td>
    <td class='centre'><%= Movie.imdb_rating %></td>
    <td><%= Movie.runtime %></td>
    <td class='centre'>
        <% if(Certificate.certificate) { %>
            <img class="centre"
                 src='/assets/img/<%= Certificate.certificate %>.png' />
        <% } else { %>
            <div class="centre">-</div>
        <% } %>
    </td>
    <td class='centre'><%= Movie.filesize %></td>
    <td class='centre'>
        <% if(Movie.hd) { %>
            <img class="centre tick_cross" src="/assets/img/tick.png">
        <% } else { %>
            <img class="centre tick_cross" src="/assets/img/cross.png">
        <% } %>
    </td>
    <td class='centre'>
        <% if(Movie.watched) { %>
            <img class="centre tick_cross" src="/assets/img/tick.png">
        <% } else { %>
            <img class="centre tick_cross" src="/assets/img/cross.png">
        <% } %>
    </td>
    <td>
        <div class="btn-group">
            <a class="btn btn-mini dropdown-toggle" data-toggle="dropdown"
               href="#">
                Actions <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">
                <li class="detail_link" data-imdb_id="<%= Movie.imdb_id %>">
                    <a><i class="icon-search"></i> Details</a>
                </li>
            </ul>
        </div>
    </td>
</script>