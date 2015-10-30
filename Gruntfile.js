module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            js: {
                src: [
                    'js/easyTooltip.js',
                    'js/jquery.mCustomScrollbar.concat.min.js',
                    'js/front/min/Timer_copy.js',
                    'js/front/min/rateList_copy.js',
                    'js/front/menu.js',
                    'js/front/frontend.js',
                    'js/front/OnlineEvent.js'
                ],
                dest: 'distribution/js/scripts.js'
            },
            css: {
                src: [
                    'css/front/frontend.css',
                    'css/front/jquery.mCustomScrollbar.css'
                ],
                dest: 'distribution/css/styles.css'
            },
        },
        uglify: {
            options: {
                stripBanners: true,
                banner: '/* <%= pkg.name %> - v<%= pkg.version %> */\n'
            },
            js: {
                files: {
                    'distribution/js/scripts.min.js': ['distribution/js/scripts.js']
                }
            }
        },
        cssmin: {
            target: {
                files: {
                    'distribution/css/styles.min.css': ['distribution/css/styles.css']
                }
            }
        },
    });

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);
};