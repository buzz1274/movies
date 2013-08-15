<script type="text/template" id="tpl-message">
    <div class="alert alert-<%= type %> message_popup">
        <button id="close_message_popup" type="button"
                class="close" data-dismiss="alert"
                onclick='interface_helper.opaque(false);'>
            &times;
        </button>
        <strong><% if(type == 'error') { %>Warning!<% } else { %>Success!<% } %></strong>
        &nbsp;&nbsp;<%= message %>
    </div>
</script>