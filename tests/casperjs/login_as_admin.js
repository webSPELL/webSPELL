casper.test.begin( "Login as Admin", 2, function suite( test ) {
    "use strict";

    casper.start( ws.url, function() {
        test.assertExists( ws.elements.loginForm, "login form is found" );
    } );

    ws.routine.login( ws.user.admin.id, ws.user.admin.password );

    casper.run( function() {
        test.done();
    } );
} );
