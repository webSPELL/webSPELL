// For the usage of Grunt please refer to
// http://24ways.org/2013/grunt-is-not-weird-and-hard/

module.exports = function(grunt) {
    "use strict";

    var javascripts = [
            "Gruntfile.js",
            "js/bbcode.js"
        ],
        templates = [
            "templates/*.html"
        ],
        phps = [
            "src/**/*.php",
            "admin/about.php",
            "admin/addons.php",
            //"admin/admincenter.php",
            //"admin/bannerrotation.php",
            //"admin/boards.php",
            //"admin/contact.php",
            //"admin/countries.php",
            //"admin/database.php",
            //"admin/faq.php",
            //"admin/faqcategories.php",
            //"admin/filecategories.php",
            //"admin/gallery.php",
            //"admin/games.php",
            //"admin/group-users.php",
            //"admin/history.php",
            //"admin/imprint.php",
            //"admin/index.php",
            //"admin/linkcategories.php",
            //"admin/lock.php",
            //"admin/members.php",
            //"admin/modrewrite.php",
            //"admin/newsletter.php",
            //"admin/overview.php",
            //"admin/page_statistic.php",
            //"admin/partners.php",
            //"admin/ranks.php",
            //"admin/rubrics.php",
            //"admin/scrolltext.php",
            //"admin/servers.php",
            //"admin/settings.php",
            //"admin/smileys.php",
            //"admin/spam.php",
            //"admin/sponsors.php",
            //"admin/spuads.php",
            //"admin/style.php",
            //"admin/update.php",
            //"admin/users.php",
            //"admin/visitor_statistic.php",
            //"admin/visitor_statistic_image.php",
            "_*.php",
            //"about.php",
            //"ajax_spamfilter.php",
            //"articles.php",
            //"asearch.php",
            //"awards.php",
            "buddies.php",
            "calendar.php",
            "cashbox.php",
            //"challenge.php",
            //"checklogin.php",
            //"clanwars.php",
            //"challenge.php",
            //"checklogin.php",
            //"clanwars.php",
            //"clanwars_details.php",
            //"code.php",
            //"comments.php",
            //"contact.php",
            //"counter.php",
            //"counter_stats.php",
            //"demos.php",
            //"downloads.php",
            //"error.php",
            //"faq.php",
            //"files.php",
            //"flags.php",
            //"forum.php",
            //"forum_topics.php",
            //"gallery.php",
            //"getlang.php",
            //"guestbook.php",
            //"history.php",
            //"imprint.php",
            //"index.php",
            //"joinus.php",
            //"latesttopics.php",
            //"links.php",
            //"linkus.php",
            //"login.php",
            //"loginoverview.php",
            //"logout.php",
            //"lostpassword.php",
            //"members.php",
            //"messenger.php",
            //"myprofile.php",
            "n*.php",
            "o*.php",
            "p*.php",
            "q*.php",
            "r*.php",
            "sc_*.php",
            //"search.php",
            //"server.php",
            //"shoutbox.php",
            //"shoutbox_content.php",
            //"smileys.php",
            //"sponsors.php",
            //"squads.php",
            //"static.php",
            //"tags.php",
            //"upload.php",
            //"usergallery.php",
            //"version.php",
            "whoisonline.php"
        ],
        csss = [ "**/*.css" ],
        excludes = [
            "!node_modules/**",
            "!components/**"
        ];

    require("logfile-grunt")(grunt, {
        filePath: "./grunt-log.txt",
        clearLogFile: true
    });

    // Project configuration.
    grunt.initConfig({
        lintspaces: {
            all: {
                src: [
                    javascripts,
                    templates,
                    excludes
                ],
                options: {
                    editorconfig: ".editorconfig"
                }
            }
        },
        jshint: {
            options: {
                jshintrc: ".jshintrc"
            },
            all: [
                javascripts,
                excludes
            ]
        },
        jscs: {
            options: {
                preset: "jquery" // See: https://contribute.jquery.org/style-guide/js/
            },
            src: [
                javascripts,
                excludes
            ]
        },
        phplint: {
            good: [ phps ]
            //bad: ["admin/fail*.php"]
        },
        phpcs: {
            application: {
                dir: [
                    phps,
                    csss,
                    excludes
                ]
            },
            options: {
                bin: "vendor/bin/phpcs",
                standard: "PSR2"
            }
        },
        //phpcpd: {
        //    application: {
        //        dir: "admin"
        //    },
        //    options: {
        //        quiet: true
        //    }
        //},
        watch: {
            options: {
                debounceDelay: 1000
            },
            js: {
                files: [
                    "js/*.js"
                ],
                tasks: [
                    "js"
                ]
            }
        }
    });

    // These plugins provide necessary tasks.
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-contrib-jshint");
    grunt.loadNpmTasks("grunt-phplint");
    grunt.loadNpmTasks("grunt-phpcs");
    grunt.loadNpmTasks("grunt-phpcpd");
    grunt.loadNpmTasks("grunt-jscs");
    grunt.loadNpmTasks("grunt-lintspaces");

    grunt.registerTask("codecheck", [
        "lintspaces",
        "jshint",
        "jscs",
        "phplint",
        "phpcs"
    ]);
    grunt.registerTask("js", [
        "jshint",
        "jscs"
    ]);
};
