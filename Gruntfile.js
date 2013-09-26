'use strict';
module.exports = function(grunt) {

	// load all grunt tasks matching the `grunt-*` pattern
	require('load-grunt-tasks')(grunt);

	grunt.initConfig({

		// watch for changes and trigger compass, jshint, uglify and livereload
		watch: {
			options: {
				atBegin: true,
				spawn: false
			},
			js: {
				files: ['lib/**/src/*.js'],
				tasks: ['uglify', 'shell:git_add_all']
			},
			less: {
				files: ['lib/**/src/*.less'],
				tasks: ['less', 'shell:git_add_all']
			}
		},

		uglify: {
			all: {
				files: [{ src: 'lib/**/src/*.js', dest: 'build/js/' }]
			}
		},

		less: {
			all: {
				files: { 'lib/**/src/*.less' : 'build/css/' }
			}
		},

		shell: {
			git_add_all: {
				command: 'git add --all'
			}
		},

		// image optimization
		imagemin: {
			dist: {
				options: {
					optimizationLevel: 7,
					progressive: true
				},
				files: [{
					expand: true,
					cwd: 'lib/',
					src: ['**/*.jpg', '**/*.png'],
					dest: 'lib/'
				}]
			}
		}

	});

	// on watch events configure jshint:all to only run on changed file
	grunt.event.on('watch', function( action, filepath ) {

		var path = require('path');

		switch( path.extname( filepath ) ) {
			//
			// Javascript -> Minify
			//
			case 'js':

				var files = [{
					src: [ filepath ], // Actual pattern(s) to match.
					dest: path.resolve( path.dirname( filepath ), '../build/', path.basename( filepath, '.js' ) + '.min.js' )
				}];

				grunt.config('uglify.all.files', files );

				break;
			//
			// LESS -> CSS
			//
			case 'less':

				var dest = path.resolve( path.dirname( filepath ), '../build/', path.basename( filepath, '.less' ) + '.css' );
				var src = filepath;

				var files = {
					dest : src
				};

				grunt.config('uglify.all.files', files );

				break;
		}
	});

	//
	// Uglify All JS
	//
	grunt.registerTask('uglify_all', 'Uglify all files and set dest to ../build/ dir, relative to the src file', function(){

		var path = require('path');
		var files = [];

		// read all subdirectories from your modules folder
		grunt.file.expand("lib/**/src/*.js").forEach(function ( filepath ) {

			files.push({
				src: [ filepath ], // Actual pattern(s) to match.
				dest: path.resolve( path.dirname( filepath ), '../build/', path.basename( filepath, '.js' ) + '.min.js' )
			});
		});

		// save the new concat config
		grunt.config.set('uglify.all.files', files);

		// when finished run the concatinations
		grunt.task.run('uglify');
		grunt.task.run('shell:git_add_all');
	});

	//
	// Compile All .less to .css
	//
	grunt.registerTask('less_all', 'Compile all .less files into .css and set dest to ../build/ dir, relative to the src file', function(){

		var path = require('path');
		var files = {};

		// read all subdirectories from your modules folder
		grunt.file.expand("lib/**/src/*.less").forEach(function ( filepath ) {

			var dest = path.resolve( path.dirname( filepath ), '../build/', path.basename( filepath, '.less' ) + '.css' );
			var src = filepath;

			files[ dest ] = src;
		});

		// save the new concat config
		grunt.config.set('less.all.files', files);

		// when finished run the concatinations
		grunt.task.run('less');
		grunt.task.run('shell:git_add_all');
	});

	// register task
	grunt.registerTask('default', ['watch']);

};