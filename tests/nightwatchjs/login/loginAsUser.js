/**
 * Created by derchris on 09/02/15.
 */

var https = require('https');
module.exports = {
    "Login" : function (browser) {
        browser
            .url("http://saucelabs.webspell.org")
            .waitForElementVisible('body', 1000)
            .setValue('input[name=ws_user]', 'user1')
            .setValue('input[name=pwd]', 'user')
            .waitForElementVisible('input[name=submit]', 1000)
            .click('input[name=submit]')
            .pause(2000)
            .assert.containsText('#rightcol', 'welcome back: user1')
            .end();
    },

    tearDown : function(callback) {
        var data = JSON.stringify({
            "passed" : (this.results.failed == 0) ? true : false,
            "tags" : ["test","google"]
        });

        var requestPath = '/rest/v1/'+ this.client.options.username +'/jobs/' + this.client.sessionId;
        try {
            console.log('Updating saucelabs', requestPath)
            var req = https.request({
                hostname: 'saucelabs.com',
                path: requestPath,
                method: 'PUT',
                auth : this.client.options.username + ':' + this.client.options.access_key,
                headers : {
                    'Content-Type': 'application/json',
                    'Content-Length' : data.length
                }
            }, function(res) {
                res.setEncoding('utf8');
                console.log('Response: ', res.statusCode, JSON.stringify(res.headers));
                res.on('data', function (chunk) {
                    console.log('BODY: ' + chunk);
                });
                res.on('end', function () {
                    console.info('Finished updating saucelabs.');
                    callback();
                });
            });

            req.on('error', function(e) {
                console.log('problem with request: ' + e.message);
            });
            req.write(data);
            req.end();
        } catch (err) {
            console.log('Error', err);
            callback();
        }

    }
};
