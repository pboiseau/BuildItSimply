module.exports = function (grunt) {

    // Init all tasks
    grunt.initConfig({

            clean: {
                css: {
                    src: ["webroot/css/dist.css"]
                },
                js: {
                    src: ["webroot/js/dist.js"]
                },
                compass: {
                    src: ["webroot/css/app.css"]
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
                }
            },
            concat: {
                options: {},
                js_dist: {
                    src: [
                        'bower_components/jquery/dist/jquery.js',
                        'bower_components/jquery-ui/jquery-ui.js',
                        'bower_components/bootstrap/dist/bootstrap.js',
                        'webroot/js/bootstrap-tokenfield.js',
                        'webroot/js/app.js'
                    ],
                    dest: 'webroot/js/dist.js'
                },
                css_dist: {
                    src: [
                        'webroot/css/bootstrap.css',
                        'bower_components/jquery-ui/themes/smoothness/jquery-ui.css',
                        'webroot/css/bootstrap-tokenfield.css',
                        'webroot/css/tokenfield-typeahead.css',
                        'webroot/css/app.css'
                    ],
                    dest: 'webroot/css/dist.css'
                }
            },
            uglify: {
                options: {},
                js_dist: {
                    files: {
                        'webroot/js/dist.js': ['webroot/js/dist.js']
                    }
                }
            },
            compass: {
                dev: {
                    options: {
                        httpPath: '/builditsimply/',
                        cssDir: 'webroot/css',
                        sassDir: 'webroot/sass',
                        imagesDir: 'webroot/images'
                    }
                },
                dist: {
                    options: {
                        httpPath: '/',
                        cssDir: 'webroot/css',
                        sassDir: 'webroot/sass',
                        imagesDir: 'webroot/images'
                    }
                }
            }


        }
    )
    ;

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-mysql-dump');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-compass');


    grunt.registerTask('sass', ['clean:compass', 'compass:dev']);
    grunt.registerTask('default', ['clean:css', 'clean:js', 'concat', 'uglify']);
    grunt.registerTask('clear', ['clean']);
    grunt.registerTask('dev', ['db_dump']);

}
;