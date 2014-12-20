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
            "**/*.php",
            "!admin/admincenter.php",
            "!admin/languages/**/*.php",
            "!install/**/*.php",
            "!languages/**/*.php",
            "!index.php"
        ],
        csss = [ "**/*.css" ],
        excludes = [
            "!node_modules/**",
            "!components/**",
            "!vendor/**",
            "!tmp/**"
        ];

    require("logfile-grunt")(grunt, {
        filePath: "./grunt-log.txt",
        clearLogFile: true
    });

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
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
            good: [
                phps,
                excludes
            ]
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
                tabWidth: "4",
                showSniffCodes: true
            }
        },
        htmllint: {
            options: {
                htmllintrc: true,
                force: true
            },
            src: templates
        },
        bootlint: {
            options: {
                stoponerror: false,
                relaxerror: [
                    "E001",
                    "E003",
                    "W001",
                    "W002",
                    "W003",
                    "W005"
                ]
            },
            files: templates
        },
        githooks: {
            all: {
                "pre-commit": "test"
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
                        from: /Copyright [0-9]{4}-[0-9]{4} by webspell.org/g,
                        to: "Copyright 2005-<%= grunt.template.today('yyyy') %> by webspell.org"
                    }
                ]
            },
            version: {
                src: [
                    "version.php"
                ],
                overwrite: true,
                replacements: [
                    {
                        from: /(\$version = ").+(";)/g,
                        to: "$version = \"<%= pkg.version %>\";"
                    }
                ]

            }
        },
        changelog: {
            release: {
                options: {
                    version: "<%= pkg.version %>"
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
    grunt.loadNpmTasks("grunt-templated-changelog");
    grunt.loadNpmTasks("grunt-bump");
    grunt.loadNpmTasks("grunt-githooks");
    grunt.loadNpmTasks("grunt-bootlint");
    grunt.loadNpmTasks("grunt-htmllint");

    grunt.registerTask("codecheck", [
        "lintspaces",
        "jshint",
        "jscs",
        "phplint",
        "phpcs",
        "htmllint",
        "bootlint"
    ]);
    grunt.registerTask("codecheck_circle", [
        "lintspaces",
        "jshint",
        "jscs",
        "phpcs",
        "bootlint"
    ]);
    grunt.registerTask("test", [
        "codecheck"
    ]);
    grunt.registerTask("js", [
        "jshint",
        "jscs"
    ]);
    grunt.registerTask("release", "Creating a new webSPELL Release", function(releaseLevel) {
        if (
            arguments.length === 0 ||
            (
            releaseLevel !== "patch" &&
            releaseLevel !== "minor" &&
            releaseLevel !== "major"
            )
        ) {
            grunt.log.error("Specify if this is a release:patch, release:minor or release:major");
        } else {
            grunt.task.run([
                "bumpOnly:" + releaseLevel,
                "replace:copyright",
                "replace:version",
                "changelog",
                "bumpCommit:" + releaseLevel
            ]);
        }
    });
    grunt.registerTask("bumpOnly", function() {
        grunt.config("bump", {
            options: {
                files: [ "package.json" ],
                createTag: false,
                commit: false,
                push: false,
                globalReplace: false
            }
        });
        return grunt.task.run("bump");
    });
    grunt.registerTask("bumpCommit", function() {
        grunt.config("bump", {
            options: {
                files: [],
                updateConfigs: [],
                commit: true,
                commitMessage: "Release v<%= pkg.version %>",
                commitFiles: [
                    "package.json",
                    "CHANGES.md"
                ],
                createTag: true,
                tagName: "v<%= pkg.version %>",
                tagMessage: "Version <%= pkg.version %>",
                push: false,
                pushTo: "origin",
                gitDescribeOptions: "--tags --always --abbrev=1 --dirty=-d",
                globalReplace: false
            }
        });
        return grunt.task.run("bump");
    });
};
