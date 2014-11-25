module.exports = function(grunt) {
	var javascripts = [
			"Gruntfile.js",
			"js/bbcode.js"
		],
		templates = [
			"templates/*.html"
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
					//"*.php",
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
	grunt.loadNpmTasks("grunt-jscs");
	grunt.loadNpmTasks("grunt-lintspaces");

	grunt.registerTask("codecheck", [
		"lintspaces",
		"jshint",
		"jscs"
	]);
	grunt.registerTask("js", [
		"jshint",
		"jscs"
	]);
};
