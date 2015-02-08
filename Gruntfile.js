// For the usage of Grunt please refer to
// http://24ways.org/2013/grunt-is-not-weird-and-hard/

module.exports = function(grunt) {
    "use strict";

    require("time-grunt")(grunt);

    var javascripts = [
            "Gruntfile.js",
            "js/bbcode.js",
            "tests/casperjs/**/*.js"
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
        releaseFiles = [
            "admin/**",
            "demos/**",
            "downloads/**",
            "images/**",
            "install/**",
            "js/**",
            "languages/**",
            "!languages/check_translations.php",
            "src/**",
            "templates/**",
            "tmp/**",
            "*",
            "!.gitignore",
            "!.scrutinizer*",
            "!.sensiolabs.yml",
            "!.travis.yml",
            "!.bowerrc",
            "!.htmllintrc",
            "!.htmlhintrc",
            "!.jshintrc",
            "!circle.yml",
            "!Gruntfile.js",
            "!grunt-log.txt",
            "!*.zip",
            "!Ruleset.xml",
            "!vendor",
            "!components",
            "!node_modules",
            "!tests"
        ],
        csss = [ "**/*.css" ],
        excludes = [
            "!node_modules/**",
            "!codestyles/**",
            "!components/**",
            "!vendor/**",
            "!tmp/**"
        ];

    require("load-grunt-tasks")(grunt, {
        pattern: [ "grunt-*" ],
        config: "package.json",
        scope: "devDependencies"
    });

    require("logfile-grunt")(grunt, {
        filePath: "./grunt-log.txt",
        clearLogFile: true
    });

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),

        scope: grunt.file.read("scope.txt"),

        type: grunt.file.read("type.txt"),

        echo: {
            inscope: "<%= scope %>",
            intype: "<%= type %>"
        },

        versioncheck: {
            options: {
                hideUpToDate: true
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
                config: ".jscsrc"
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

        htmlhint: {
            options: {
                htmlhintrc: ".htmlhintrc", // https://github.com/yaniswang/HTMLHint/wiki/Rules
                force: true
            },
            html1: {
                src: [ "templates/*.html" ]
            }
        },

        bootlint: {
            options: {
                stoponerror: true,
                relaxerror: [
                    "E001", // Document is missing a DOCTYPE declaration
                    "E003", // .row that were not children of a grid column
                    "W001", // <head> is missing UTF-8 charset
                    "W002", // <head> is missing X-UA-Compatible <meta> tag
                    "W003", // <head> is missing viewport <meta> tag that enables responsiveness
                    "W005", // Unable to locate jQuery
                    "W014" // Carousel controls and indicators should use `href` or `data-target`
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
                        from: /Copyright [0-9]{4}-[0-9]{4}/g,
                        to: "Copyright 2005-<%= grunt.template.today('yyyy') %>"
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

        karma: {
            unit: {
                configFile: "karma.conf.js"
            },
            continuous: {
                configFile: "karma.conf.js",
                singleRun: true,
                browsers: [ "PhantomJS" ]
            }
        },

        casperjs: {
            options: {
                casperjsOptions: [
                    //"--engine=slimerjs",
                    "--includes=tests/casperjs/config.js," +
                    "tests/casperjs/functions/login.js"
                ]
            },
            files: [
                "tests/casperjs/login_as_admin.js"
            ]
        },

        watch: {
            options: {
                debounceDelay: 1000
            },
            all: {
                files: [
                    phps,
                    javascripts,
                    templates,
                    csss
                ],
                tasks: [
                    "codecheck_newer"
                ]
            },
            html: {
                files: [
                    templates
                ],
                tasks: [
                    "html"
                ]
            },
            js: {
                files: [
                    javascripts
                ],
                tasks: [
                    "js"
                ]
            }
        },

        exec: {
            quickcheck: {
                command: "sh ./qphpcs.sh",
                stdout: true,
                stderr: true
            }
        },

        compress: {
            main: {
                options: {
                    archive: "webspell.zip"
                },
                src:releaseFiles
            },
            release: {
                options: {
                    archive: "webSPELL-<%= pkg.version %>.zip"
                },
                src:releaseFiles
            }
        }
    });

    grunt.registerTask("codecheck", [
        "js",
        "php",
        "html"
    ]);

    grunt.registerTask("codecheck_newer", [
        "newer:js",
        "newer:phplint",
        "newer:phpcs",
        "newer:html"
    ]);

    grunt.registerTask("codecheck_circle", [
        "jshint",
        "jscs",
        "phpcs",
        "htmlhint",
        "htmllint",
        "bootlint"
    ]);

    grunt.registerTask("codecheck_travis", [
        "jshint",
        "jscs",
        "phplint",
        "phpcs",
        "htmlhint",
        "htmllint",
        "bootlint"
    ]);

    grunt.registerTask("html", [
        "htmlhint",
        "htmllint",
        "bootlint"
    ]);

    grunt.registerTask("js", [
        "jshint",
        "jscs",
        "karma:continuous"
    ]);

    grunt.registerTask("php", [
        "phplint",
        "phpcs"
    ]);

    grunt.registerTask("git", [
        "grunt-commit-message-verify"
    ]);

    grunt.registerTask("test", [
        "codecheck",
        "git"
    ]);

    grunt.registerTask("quick", [
        "exec:quickcheck"
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
                "bumpCommit:" + releaseLevel,
                "compress:release"
            ]);
        }
    });

    grunt.registerTask("bumpOnly", function() {
        grunt.config("bump", {
            options: {
                files: [
                    "package.json",
                    "bower.json"
                ],
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

    grunt.registerMultiTask("echo", "Echo back input", function() {
        grunt.log.writeln(this.data);
    });

    grunt.config("grunt-commit-message-verify", {
        minLength: 0,
        maxLength: 3000,

        // first line should be both concise and informative
        minFirstLineLength: 20,
        maxFirstLineLength: 60,

        // this is a good default to prevent overflows in shell console and Github UI
        maxLineLength: 80,

        regexes: {
            "check type": {
                regex: /^(chore|docs|feat|fix|refactor|style|test|wip)\(/i,
                explanation:
                    "The commit should start with a type like fix, feat, or chore. " +
                    "See type.txt for a full list."
            },
            "check scope": {
                /* jscs ignore:start */
                regex: /\((about|addons|admincenter|articles|awards|bannerrotation|buddies|buildtools|calendar|cashbox|challenge|clanwars|\bcode\b|\bcodestyle\b|comments|contact|core|counter|countries|database|demos|error|faq|files|flags|forum|gallery|games|groups|guestbook|history|images|imprint|index|install|joinus|languages|links|linkus|lock|login|members|messenger|modrewrite|navigation|\bnews\b|\bnewsletter\b|out|overview|partners|picture|polls|profile|ranks|rating|register|report|rubrics|search|server|settings|shoutbox|smileys|spam|sponsors|squads|static|statistics|\bstyles\b|\bstylesheet\b|tags|templates|update|upload|users|version|whoisonline)\)/i,
                /* jscs ignore:end */
                explanation:
                    "The commit should include a scope like (forum), (news) or (buildtools). " +
                    "See scope.txt for a full list."
            },
            "check close github issue": {
                /* jscs ignore:start */
                regex: /((?=(((close|resolve)(s|d)?)|fix(es|ed)?))((((close|resolve)(s|d)?)|fix(es|ed)?) #\d+))/ig,
                /* jscs ignore:end */
                explanation:
                    "If closing an issue, the commit should include github issue no like " +
                    "fix #123, closes #123 or resolves #123"
            },
            "check subject format": {
                regex: /(: \w+.*)/ig,
                explanation: "The commit message subject should look like this ': <subject>'"
            }
        },
        skipCheckAfterIndent: false,
        forceSecondLineEmpty: true,
        messageOnError: "",
        shellCommand: "git log --format=%B --no-merges -n 1"
    });
};
