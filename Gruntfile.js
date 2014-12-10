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
            "!admin/admincenter.php",
            "!admin/boards.php",
            "!admin/contact.php",
            "!admin/countries.php",
            "!admin/database.php",
            "!admin/faq.php",
            "!admin/faqcategories.php",
            "!admin/filecategories.php",
            "!admin/gallery.php",
            "!admin/games.php",
            "!admin/group-users.php",
            "!admin/history.php",
            "!admin/imprint.php",
            "!admin/index.php",
            "!admin/linkcategories.php",
            "!admin/lock.php",
            "!admin/members.php",
            "!admin/modrewrite.php",
            "!admin/newsletter.php",
            "!admin/overview.php",
            "!admin/page_statistic.php",
            "!admin/partners.php",
            "!admin/ranks.php",
            "!admin/rubrics.php",
            "!admin/scrolltext.php",
            "!admin/servers.php",
            "!admin/settings.php",
            "!admin/smileys.php",
            "!admin/spam.php",
            "!admin/sponsors.php",
            "!admin/spuads.php",
            "!admin/style.php",
            "!admin/update.php",
            "!admin/users.php",
            "!admin/visitor_statistic.php",
            "!admin/visitor_statistic_image.php",
            "!install/**/*.php",
            "!languages/**/*.php",
            "src/**/*.php",
            "*.php",
            "!index.php"
        ],
        csss = [ "**/*.css" ],
        excludes = [
            "!node_modules/**",
            "!components/**",
            "!vendor/**"
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
                standard: "Ruleset.xml",
                showSniffCodes: true
            }
        },
        replace: {
            copyright: {
                src: [
                    "**/*.{css,html,js,md,php,txt}",
                    excludes,
                    "!Gruntfile.js"
                ],
                overwrite: true,
                replacements: [
                    {
                        from: /(Copyright).+(webspell.org)/g,
                        to: "Copyright 2005-<%= grunt.template.today('yyyy') %> by webspell.org"
                    }
                ]
            }
        },
        changelog: {
            release: {
                options: {
                    version: '4.3.0'
                }
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
    grunt.loadNpmTasks("grunt-text-replace");
    grunt.loadNpmTasks('grunt-templated-changelog');

    grunt.registerTask("codecheck", [
        "lintspaces",
        "jshint",
        "jscs",
        "phplint",
        "phpcs"
    ]);

    grunt.registerTask("codecheck_circle", [
        "lintspaces",
        "jshint",
        "jscs",
        "phpcs"
    ]);

    grunt.registerTask("js", [
        "jshint",
        "jscs"
    ]);

    grunt.registerTask("release", [
        "replace:copyright"
    ]);
};
