module.exports = function (grunt) {

	require('logfile-grunt')(grunt, {
		filePath: './grunt-log.txt',
		clearLogFile: true
	});

	// Project configuration.
	grunt.initConfig({
		jshint: {
			all: ['Gruntfile.js', 'js/bbcode.js']
		},
		jscs: {
			options: {
				preset: 'jquery', // See: https://contribute.jquery.org/style-guide/js/
				validateLineBreaks: null, // Needs to be set because of Windows machines
			},
			src: [
				'js/bbcode.js',
			]
		},
		watch: {
			options: {
				debounceDelay: 1000
			},
			js: {
				files: [
					'js/*.js'
				],
				tasks: [
					'js'
				]
			},
		}
	});

	// These plugins provide necessary tasks.
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks("grunt-jscs");

	grunt.registerTask('js', [
		'jshint',
		'jscs'
	]);
};