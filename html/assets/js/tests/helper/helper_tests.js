"use strict";
define(['../../helper/helper'],
    function(Helper) {
        var helperTests = function() {
            module("Helper Tests");
            test('convert time should convert to minutes to hrs minutes', function() {
                equal(Helper.convertToTime(45), '45mins', 'The return should be 45mins');
                equal(Helper.convertToTime(60), '1hr', 'The return should be 1hr');
                equal(Helper.convertToTime(119), '1hr 59mins', 'The return should be 1hr 59mins');
                equal(Helper.convertToTime(120), '2hrs', 'The return should be 2hrs');
                equal(Helper.convertToTime(135), '2hrs 15mins', 'The return should be 2hrs 15mins');
                equal(Helper.convertToTime('a'), false, 'The return should be false');
                equal(Helper.convertToTime(-10), false, 'The return should be false');
            });
            test('Query string has duplicate page var(&p=) removed', function() {
                equal(Helper.removePageFromQueryString(), '', 'The return should be empty');
            });
        };

        return {helperTests: helperTests};

    }
);