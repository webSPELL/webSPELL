/**
 * Created by derchris on 09/02/15.
 */

var https = require('https');
module.exports = {
    "Test Google" : function (browser) {
        browser
            .url("http://www.google.com")
            .waitForElementVisible('body', 1000)
            .setValue('input[name=q]', 'webspell')
            .waitForElementVisible('button[name=btnG]', 1000)
            .click('button[name=btnG]')
            .pause(1000)
            .assert.containsText('#main', 'webSPELL.org CMS » Free Content Management System')
            .end();
    },

    tearDown : function(callback) {
        var data = JSON.stringify({
            "passed" : true,
            "tags" : ["test","google"]
        });

        var requestPath = '/rest/v1/'+ this.client.options.username +'/jobs/' + this.client.sessionId;
        try {
            console.log('Updaing saucelabs', requestPath)
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
