<script type="text/template" id="tpl-movie-paging">
    <% if(totalPages > 1) { %>
        <ul>
            <li class="<% if(page <= 1) {%>disabled<%}%>">
                <% if(page <= 1) {%>
                    <a>First</a>
                    <a>Prev</a>
                <% } else { %>
                    <a href="#p=1&<%= UrlParams.remove_page_from_query_string() %>">
                        First
                    </a>
                    <a href="#p=1<%= (parseInt(page) - 1) %>&<%= UrlParams.remove_page_from_query_string() %>">
                        Prev
                    </a>
                <% } %>
            </li>
            <% if(totalPages > 10 && page > 6) {
                var loopEnd = parseInt(page) + 5;
                if(loopEnd > totalPages) {
                    loopEnd = totalPages;
                }
                var loopStart = parseInt(loopEnd) - 9;
            } else {
                var loopStart = 1;
                var loopEnd = 10;
            }
            %>
            <% for(i = loopStart; i <= loopEnd; i++) { %>
                <li class="<% if(page == i) {%>disabled<%}%>">
                    <% if(page == i) { %>
                        <a><%= i %></a>
                    <% } else if(i <= totalPages) { %>
                        <a href="#p=<%= i %>&<%= UrlParams.remove_page_from_query_string() %>">
                            <%= i %>
                        </a>
                    <% } %>
                </li>
            <% } %>
            <li class="<% if(page >= totalPages) {%>disabled<%}%>">
                <% if(page >= totalPages) {%>
                    <a>Next</a>
                    <a>Last</a>
                <% } else { %>
                    <a href="#p=<%= (parseInt(page) + 1) %><%= UrlParams.remove_page_from_query_string() %>">
                        Next
                    </a>
                    <a href="#p=<%= totalPages %><%= UrlParams.remove_page_from_query_string() %>">
                        Last
                    </a>
                <% } %>
            </li>
        </ul>
    <% } %>
</script>
