module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-clean');

	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),
		less: {
			dev: {
				files: [{
					expand: true,
					cwd: 'styles/',
					src: ['**/*.less'],
					dest: 'styles/',
					ext: '.css'
				}]
			},
			prod: {
				options: {
					compress: true
				},
				files: [{
					expand: true,
					cwd: 'styles/',
					src: ['**/*.less'],
					dest: 'styles/',
					ext: '.css'
				}]
			}
		},
		cssmin: {
			css: {
				files: [{
					expand: true,
					cwd: 'styles/',
					src: ['**/*.css', '!colorbox.css'],
					dest: 'styles/',
					ext: '.css'
				}]
			}
		},
		clean: {
			less: ['styles/**/*.less']
		},
		watch: {
			less: {
				files: ['styles/**/*.less'],
				tasks: ['less:dev']
			}
		}
	});

	grunt.registerTask('default', ['less:dev']);
	grunt.registerTask('release', ['less:prod', 'cssmin', 'clean:less']);
};