/**
 * Created by derchris on 27/02/15.
 */

var https = require('https');
var regUser = 'regUser-'+ Math.floor(new Date() / 1000);
module.exports = {
    "Register" : function (browser) {
        browser
            .url("http://saucelabs.webspell.org/index.php?site=register")
            .waitForElementVisible('body', 1000)
            .setValue('input[name=nickname]', regUser)
            .setValue('input[name=username]', regUser)
            .setValue('input[name=pwd1]', regUser)
            .setValue('input[name=pwd2]', regUser)
            .setValue('input[name=mail]', regUser +'@webspell.org')
            .getText('.captcha-img', function(result) {
                this.setValue('input[name=captcha]', result.value)
            })
            .waitForElementVisible('input[name=save]', 1000)
            .click('input[name=save]')
            .assert.containsText('#maincol', 'Your registration was successful. You will receive an email with an account activation link shortly.')
            .pause(1000)
            .url("http://saucelabs.webspell.org")
            .waitForElementVisible('body', 1000)
            .setValue('input[name=ws_user]', 'admin')
            .setValue('input[name=pwd]', 'admin')
            .waitForElementVisible('input[name=submit]', 1000)
            .click('input[name=submit]')
            .pause(2000)
            .assert.containsText('#rightcol', 'welcome back: admin')
            .url("http://saucelabs.webspell.org/admin/admincenter.php")
            .waitForElementVisible('body', 1000)
            .assert.containsText('.pad>h1', 'Welcome to your webSPELL AdminCenter')
            .url("http://saucelabs.webspell.org/admin/admincenter.php?site=users")
            .useXpath()
            .setValue('html/body/table/tbody/tr[3]/td[4]/div/table[1]/tbody/tr[1]/td[2]/input[2]', regUser)
            .useCss()
            .pause(500)
            .click('#searchresult>a')
            .useXpath()
            .pause(1000)
            .click('html/body/table/tbody/tr[3]/td[4]/div/table[2]/tbody/tr[2]/td[5]/a')
            .useCss()
            .url("http://saucelabs.webspell.org/logout.php")
            .pause(1000)
            .url("http://saucelabs.webspell.org")
            .waitForElementVisible('body', 1000)
            .setValue('input[name=ws_user]', regUser)
            .setValue('input[name=pwd]', regUser)
            .waitForElementVisible('input[name=submit]', 1000)
            .click('input[name=submit]')
            .pause(1000)
            .assert.containsText('#rightcol', 'welcome back: '+ regUser)
            .url("http://saucelabs.webspell.org/index.php?site=myprofile")
            .assert.containsText('.page-header>h2>a', 'my profile')
            .setValue('textarea[name=usertext]', regUser)
            .setValue('select[id=input-flag]', 'de')
            .setValue('input[name=town]', regUser)
            .setValue('input[name=birthday]', '30.12.1999')
            .waitForElementVisible('input[name=submit]', 1000)
            .click('input[name=submit]')
            .end();
    },

    tearDown : function(callback) {
        var data = JSON.stringify({
            "passed" : (this.results.failed == 0) ? true : false,
            "tags" : ["register"]
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
