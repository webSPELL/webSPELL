/**
 * Created by derchris on 09/02/15.
 */

module.exports = {
    "Test Google" : function (browser) {
        browser
            .url("http://www.google.com")
            .waitForElementVisible('body', 1000)
            .setValue('input[type=text]', 'webspell')
            .waitForElementVisible('button[name=btnG]', 1000)
            .click('button[name=btnG]')
            .pause(1000)
            .assert.containsText('#main', 'webSPELL.org CMS » Free Content Management System')
            .end();
    }
};