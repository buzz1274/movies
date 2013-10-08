<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Movies</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/assets/img/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/assets/img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css"
              href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css"
              rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="/assets/css/styles.css" />
    </head>
    <body>
        <div id="opaque"></div>
        <div id="navbar" class="navbar navbar-fixed-top"></div>
        <div class="container"></div>
        <div class="container-fluid">
            <div id="content" class="row-fluid">
                <div id="movies_search" class="span4"></div>
                <div id="movies_list" class="span8">
                    <div class="message_popup_container"></div>
                    <div id="loading"><img src="/assets/img/spinner.gif"></div>
                    <table class="table table-bordered table-condensed"
                           style="display:none;" id="movies_table">
                    </table>
                    <div id="pagination" class="pagination"></div>
                </div>
            </div>
        </div>

        <?php echo $this->element('tpl-navbar'); ?>
        <?php echo $this->element('tpl-login'); ?>
        <?php echo $this->element('tpl-message-popup'); ?>
        <?php echo $this->element('tpl-movie-search'); ?>
        <?php echo $this->element('tpl-movie-paging'); ?>
        <?php echo $this->element('tpl-movie-list-header'); ?>
        <?php echo $this->element('tpl-movie-list-no-results'); ?>
        <?php echo $this->element('tpl-movie-list-item'); ?>
        <?php echo $this->element('tpl-movie-list-footer'); ?>
        <?php echo $this->element('tpl-movie-details'); ?>
        <?php echo $this->element('tpl-movie-downloaded'); ?>
        <?php echo $this->element('tpl-movie-downloaded-header'); ?>

        <script type="text/javascript"
            src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js">
        </script>
        <script type="text/javascript"
            src="http://code.jquery.com/ui/1.9.1/jquery-ui.min.js">
        </script>
        <script type="text/javascript"
            src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.3.3/underscore-min.js">
        </script>
        <script type="text/javascript"
            src="http://cdnjs.cloudflare.com/ajax/libs/backbone.js/0.9.2/backbone-min.js">
        </script>
        <script type="text/javascript"
            src="http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js">
        </script>

        <script type="text/javascript" src="/assets/js/helper/state.js"></script>
        <script type="text/javascript" src="/assets/js/helper/helper.js"></script>
        <script type="text/javascript" src="/assets/js/helper/interface_helper.js"></script>
        <script type="text/javascript" src="/assets/js/models/movies.js"></script>
        <script type="text/javascript" src="/assets/js/models/user.js"></script>
        <script type="text/javascript" src="/assets/js/collections/movies.js"></script>
        <script type="text/javascript" src="/assets/js/views/user.js"></script>
        <script type="text/javascript" src="/assets/js/router.js"></script>
        <script type="text/javascript" src="/assets/js/views/movies.js"></script>
        <script type="text/javascript" src="/assets/js/main.js"></script>
    </body>
</html>