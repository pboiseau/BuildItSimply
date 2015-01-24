module.exports = function(grunt) {

	// Init all tasks
	grunt.initConfig({

		clean: {
			css: {
				src: ["webroot/css/app.css"]
			},
			js: {
				src: ["webroot/js/app.js"]
			}
		},
		db_dump: {
			"local": {
				"options": {
					"title": "Build My Project",
					"database": "buildmyproject",
					"user": "root",
					"pass": "root",
					"host": "localhost",
					"backup_to": "dump/dump_buildmyproject.sql"
				}
			},
		},
		concat: {
			options: {},
			js_dist: {
				src: ['webroot/js/*.js'],
				dest: 'webroot/js/app.js',
			},
			css_dist: {
				src: ['webroot/css/*.css'],
				dest: 'webroot/css/app.css'
			}
		},
		uglify: {
			options: {},
			dist: {
				files: {
					'webroot/js/app.js': ['webroot/js/app.js']
				}
			}
		}

	});

	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-mysql-dump');
	grunt.loadNpmTasks('grunt-contrib-clean');

	grunt.registerTask('default', ['clean', 'concat', 'uglify']);
	grunt.registerTask('dev', ['db_dump']);

};