define(function(require, exports, module) {
    "use strict";

    var Backbone = require('backbone'),
        $ = require('bootstrap'),
        PagingTemplate = require('text!templates/paging.html'),
        State = require('helper/state'),
        Helper = require('helper/helper'),
        summary = require('models/movie/summary'),
        loanedSummary = require('models/media/loaned_summary');

    module.exports = Backbone.View.extend({
        tagName:"div",
        template:_.template(PagingTemplate),
        events: {
            'click img.paging_link': 'paging'
        },
        initialize: function() {
            $('#pagination').css('display', 'none');
        },
        render: function(type) {
            if(type == 'loanedSummary') {
                var data = {totalPages: loanedSummary.get('totalPages'),
                            page: loanedSummary.get('page'),
                            helper: Helper};
            } else if(type == 'summary') {
                var data = {totalPages: summary.get('totalPages'),
                            page: summary.get('page'),
                            helper: Helper};
            }

            if(data.totalPages > 1) {
                $(this.el).append(this.template(data));
                $('#pagination').css('display', 'block');
            }
            return this;
        },
        paging:function(ev) {
            var paging_method = $(ev.currentTarget).attr('data_link_action');
            var stateParams = State.getParams();
            if(paging_method == 'first') {
                State.setStateParams('p', 1);
            } else if(paging_method == 'last') {
                State.setStateParams('p', summary.get('totalPages'));
            } else if(paging_method == 'prev' && stateParams.p > 1) {
                State.setStateParams('p', parseInt(stateParams) - 1);
            } else if(paging_method == 'next' &&
                      stateParams.p < summary.get('totalPages')) {
                State.setStateParams('p', parseInt(stateParams) - 1);
            }
            Backbone.history.navigate(State.constructQueryString(), {'trigger':true});
        }
    });
});