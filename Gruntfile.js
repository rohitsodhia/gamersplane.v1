module.exports = function(grunt) {
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-less');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
	grunt.loadNpmTasks('grunt-contrib-clean');
	grunt.loadNpmTasks('grunt-shell');

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
		watch: {
			less: {
				files: ['styles/**/*.less'],
				tasks: ['less:dev']
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
			css: ['styles/**/*.css', '!styles/colorbox.css']
		},
		uglify: {
			js: {
				files: [{
					expand: true,
					cwd: 'javascript/',
					src: ['*.js'],
					dest: 'javascript/'
				}]
			}
		}
	});

	grunt.registerTask('default', ['less:dev']);
	grunt.registerTask('release', ['clean:css', 'less:prod', 'cssmin', 'uglify']);
};