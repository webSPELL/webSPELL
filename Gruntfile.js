module.exports = function(grunt) {
    var javascripts = [
            "Gruntfile.js",
            "js/bbcode.js"
        ],
        templates = [
            "templates/*.html"
        ],
        phps = [
            "admin/about.php",
            "admin/addons.php"
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
                    templates
                ],
                options: {
                    editorconfig: ".editorconfig"
                }
            }
        },
        jshint: {
            all: [ javascripts ]
        },
        jscs: {
            options: {
                preset: "jquery", // See: https://contribute.jquery.org/style-guide/js/
                validateLineBreaks: null // Needs to be set because of Windows machines
            },
            src: [ javascripts ]
        },
        phplint: {
            good: [ phps ]
            //bad: ["admin/fail*.php"]
        },
        phpcs: {
            application: {
                dir: [ phps ]
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
        //"lintspaces",
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
