<script type="text/template" id="tpl-movie-details">
    <tr id="<%= Movie.imdb_id %>">
        <td class="movie_details span12" colspan="10">
            <div class="span4">
                <% if(Movie.has_image) { %>
                    <img class="movie_image"
                         src="/assets/img/movies/<%= Movie.imdb_id %>.jpg" />
                <% } else { %>
                    <div class="no_image_holder">
                        <div>
                            <p>No Cover Image</p>
                        </div>
                    </div>
                <% } %>
            </div>
            <div class="span8">
                <%= Movie.synopsis %>
                <dl class="directors clearfix">
                    <dt><strong>Director(s):</strong></dt>
                    <% _.each(Director, function(director) { %>
                        <dd class="pull-left">
                            <span class="director_link link"
                               data-person_id="<%= director.person_id %>">
                                <a></1><%= director.person_name %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="actors clearfix">
                    <dt><strong>Actors(s):</strong></dt>
                    <% _.each(Actor, function(actor) { %>
                        <dd class="pull-left">
                            <span class="actor_link link"
                               data-person_id="<%= actor.person_id %>">
                                <a><%= actor.person_name %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="genres clearfix">
                    <dt><strong>Genre(s):</strong></dt>
                    <% _.each(Genre, function(genre) { %>
                        <dd class="pull-left">
                            <span class="genre_link link"
                               data-genre_id="<%= genre.genre_id %>">
                                <a><%= genre.genre %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="genres clearfix">
                    <dt><strong>Keyword(s):</strong></dt>
                    <% _.each(Keyword, function(keyword) { %>
                        <dd class="pull-left">
                            <span class="keyword_link link"
                               data-keyword_id="<%= keyword.keyword_id %>">
                                <a><%= keyword.keyword %></a>
                            </span>
                        </dd>
                    <% }); %>
                </dl>
                <dl class="clearfix">
                    <dt><strong>Archived:</strong></dt>
                    <dd class="pull-left">
                        <% if(Movie.archived) { %>
                            <img style="padding-top:5px;" height="15" width="15"
                                 src='/assets/img/tick.png' />
                        <% } else { %>
                            <img style="padding-top:5px;" height="15" width="15"
                                 src='/assets/img/cross.png' />
                        <% } %>
                    </dd>
                </dl>
                <dl class="clearfix">
                    <dt><strong>Path:</strong></dt>
                    <dd class="puil-left"><%= Movie.path %></dd>
                </dl>
            </div>
        </td>
    </tr>
</script>