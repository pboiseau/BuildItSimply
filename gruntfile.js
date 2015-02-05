module.exports = function(grunt) {

	// Init all tasks
	grunt.initConfig({

		clean: {
			css: {
				src: ["webroot/css/dist.css"]
			},
			js: {
				src: ["webroot/js/dist.js"]
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
				dest: 'webroot/js/dist.js',
			},
			css_dist: {
				src: ['webroot/css/*.css'],
				dest: 'webroot/css/dist.css'
			}
		},
		uglify: {
			options: {},
			dist: {
				files: {
					'webroot/js/dist.js': ['webroot/js/dist.js']
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