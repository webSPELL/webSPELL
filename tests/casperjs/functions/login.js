ws.routine.login = function( id, password ) {
    "use strict";

    casper.then( function() {
        this.fill(
            ws.elements.loginForm, {
                "ws_user": id,
                "pwd": password
            },
            true
        );
    } );

    casper.then( function() {
        this.test.assertSelectorDoesntHaveText(
            "form[name=login]",
            "Login form does not exist"
        );
    } );
};
