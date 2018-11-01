// AdminLTE Gruntfile
module.exports = function (grunt) { // jshint ignore:line
  'use strict';

  grunt.initConfig({
    pkg   : grunt.file.readJSON('package.json'),
    watch : {
      less : {
        // Compiles less files upon saving
        files: ['build/less/*.less'],
        tasks: ['less:development', 'less:production', 'replace', 'notify:less']
      },
      js   : {
        // Compile js files upon saving
        files: ['build/js/*.js'],
        tasks: ['js', 'notify:js']
      },
      skins: {
        // Compile any skin less files upon saving
        files: ['build/less/skins/*.less'],
        tasks: ['less:skins', 'less:minifiedSkins', 'notify:less']
      }
    },
    // Notify end of tasks
    notify: {
      less: {
        options: {
          title  : 'AdminLTE',
          message: 'LESS finished running'
        }
      },
      js  : {
        options: {
          title  : 'AdminLTE',
          message: 'JS bundler finished running'
        }
      }
    },

    copy: {
      main: {
        files: [
          // jQuery
          {expand: true, cwd: 'node_modules/jquery/dist', src: ['**'], dest: '../../public/js/', filter: 'isFile'},

          // Bootstrap
          {expand: true, cwd: 'node_modules/bootstrap/dist/fonts', src: ['**'], dest: '../../public/fonts/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/bootstrap/dist/css', src: ['**'], dest: '../../public/css/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/bootstrap/dist/js', src: ['**'], dest: '../../public/js/', filter: 'isFile'},

          // Font-awesome
          {expand: true, cwd: 'node_modules/font-awesome/fonts', src: ['**'], dest: '../../public/fonts/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/font-awesome/css', src: ['**'], dest: '../../public/css/', filter: 'isFile'},

          // Ionic icons
          {expand: true, cwd: 'node_modules/ionicons/dist/fonts', src: ['**'], dest: '../../public/fonts/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/ionicons/dist/css', src: ['**'], dest: '../../public/css/', filter: 'isFile'},

          //Comments
          {expand: true, cwd: 'node_modules/jquery-comments/js', src: ['**'], dest: '../../public/js/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/jquery-comments/css', src: ['jquery-comments.css'], dest: '../../public/css/'},

          //Lightslider
          {expand: true, cwd: 'node_modules/lightslider/dist/img', src: ['**'], dest: '../../public/img/', filter: 'isFile'},

          //Weather icons
          {expand: true, cwd: 'node_modules/weather-icons2/font', src: ['**'], dest: '../../public/fonts/', filter: 'isFile'},

          //Flag icons
          {expand: true, cwd: 'node_modules/flag-icon-css/flags/1x1', src: ['**'], dest: '../../public/flags/1x1/', filter: 'isFile'},
          {expand: true, cwd: 'node_modules/flag-icon-css/flags/4x3', src: ['**'], dest: '../../public/flags/4x3/', filter: 'isFile'},

          //Video.js
          {expand: true, cwd: 'node_modules/video.js/dist', src: ['video-js.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/video.js/dist', src: ['video.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/videojs-youtube/dist', src: ['Youtube.min.js'], dest: '../../public/js/'},

          //Froala Editor
          {expand: true, cwd: 'node_modules/froala-editor/css', src: ['froala_editor.pkgd.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/froala-editor/css', src: ['froala_style.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/froala-editor/js', src: ['froala_editor.pkgd.min.js'], dest: '../../public/js/'},

          //Select 2
          {expand: true, cwd: 'node_modules/select2/dist/css', src: ['select2.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/select2/dist/js', src: ['select2.full.min.js'], dest: '../../public/js/'},

          //Moment + timezone
          {expand: true, cwd: 'node_modules/moment/min', src: ['moment-with-locales.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/moment-timezone/builds', src: ['moment-timezone-with-data.min.js'], dest: '../../public/js/'},

          //Date picker & Time picker
          {expand: true, cwd: 'node_modules/bootstrap-datepicker/dist/css', src: ['bootstrap-datepicker3.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/bootstrap-datepicker/dist/js', src: ['bootstrap-datepicker.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/bootstrap-datepicker/dist/locales', src: ['bootstrap-datepicker.ro.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/bootstrap-datepicker/dist/locales', src: ['bootstrap-datepicker.ru.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/bootstrap-timepicker/js', src: ['bootstrap-timepicker.js'], dest: '../../public/js/'},

          //File Upload
          {expand: true, cwd: 'node_modules/dm-file-uploader/dist/css', src: ['jquery.dm-uploader.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/dm-file-uploader/dist/js', src: ['jquery.dm-uploader.min.js'], dest: '../../public/js/'},

          //Cropper
          {expand: true, cwd: 'node_modules/cropperjs/dist', src: ['cropper.min.css'], dest: '../../public/css/'},
          {expand: true, cwd: 'node_modules/cropperjs/dist', src: ['cropper.min.js'], dest: '../../public/js/'},
          {expand: true, cwd: 'node_modules/jquery-cropper/dist', src: ['jquery-cropper.min.js'], dest: '../../public/js/'},
        ],
      }
    },

    replace: {
      font: {
        src: ['node_modules/weather-icons2/css/weather-icons.css'],
        dest: 'build/less/weather-icons.css',
        replacements: [{
          from: '../font/',
          to: '../fonts/'
        }]
      }
    },

    // 'less'-task configuration
    // This task will compile all less files upon saving to create both AdminLTE.css and AdminLTE.min.css
    less  : {
      // Production compressed version
      production   : {
        options: {
          compress: true
        },
        files  : {
          // compilation.css  :  source.less
          '../../public/css/ungheni.min.css'                     : [
            'build/less/weather-icons.css',
            'node_modules/flag-icon-css/css/flag-icon.min.css',
            'node_modules/font-awesome-animation/dist/font-awesome-animation.min.css',
            'build/less/AdminLTE.less'
          ],
          // AdminLTE without plugins
          '../../public/css/ungheni-without-plugins.min.css' : 'build/less/AdminLTE-without-plugins.less',
          // Separate plugins
          '../../public/css/ungheni-fullcalendar.min.css'    : 'build/less/fullcalendar.less',
          '../../public/css/ungheni-bootstrap-social.min.css': 'build/less/bootstrap-social.less',
          '../../public/css/timepicker.min.css': 'node_modules/bootstrap-timepicker/css/timepicker.less'
        }
      },
      // Non minified skin files
      skins        : {
        files: {
          '../../public/css/skins/skin-blue.css'        : 'build/less/skins/skin-blue.less',
          '../../public/css/skins/skin-black.css'       : 'build/less/skins/skin-black.less',
          '../../public/css/skins/skin-yellow.css'      : 'build/less/skins/skin-yellow.less',
          '../../public/css/skins/skin-green.css'       : 'build/less/skins/skin-green.less',
          '../../public/css/skins/skin-red.css'         : 'build/less/skins/skin-red.less',
          '../../public/css/skins/skin-purple.css'      : 'build/less/skins/skin-purple.less',
          '../../public/css/skins/skin-blue-light.css'  : 'build/less/skins/skin-blue-light.less',
          '../../public/css/skins/skin-black-light.css' : 'build/less/skins/skin-black-light.less',
          '../../public/css/skins/skin-yellow-light.css': 'build/less/skins/skin-yellow-light.less',
          '../../public/css/skins/skin-green-light.css' : 'build/less/skins/skin-green-light.less',
          '../../public/css/skins/skin-red-light.css'   : 'build/less/skins/skin-red-light.less',
          '../../public/css/skins/skin-purple-light.css': 'build/less/skins/skin-purple-light.less',
          '../../public/css/skins/_all-skins.css'       : 'build/less/skins/_all-skins.less'
        }
      },
      // Skins minified
      minifiedSkins: {
        options: {
          compress: true
        },
        files  : {
          '../../public/css/skins/skin-blue.min.css'        : 'build/less/skins/skin-blue.less',
          '../../public/css/skins/skin-black.min.css'       : 'build/less/skins/skin-black.less',
          '../../public/css/skins/skin-yellow.min.css'      : 'build/less/skins/skin-yellow.less',
          '../../public/css/skins/skin-green.min.css'       : 'build/less/skins/skin-green.less',
          '../../public/css/skins/skin-red.min.css'         : 'build/less/skins/skin-red.less',
          '../../public/css/skins/skin-purple.min.css'      : 'build/less/skins/skin-purple.less',
          '../../public/css/skins/skin-blue-light.min.css'  : 'build/less/skins/skin-blue-light.less',
          '../../public/css/skins/skin-black-light.min.css' : 'build/less/skins/skin-black-light.less',
          '../../public/css/skins/skin-yellow-light.min.css': 'build/less/skins/skin-yellow-light.less',
          '../../public/css/skins/skin-green-light.min.css' : 'build/less/skins/skin-green-light.less',
          '../../public/css/skins/skin-red-light.min.css'   : 'build/less/skins/skin-red-light.less',
          '../../public/css/skins/skin-purple-light.min.css': 'build/less/skins/skin-purple-light.less',
          '../../public/css/skins/_all-skins.min.css'       : 'build/less/skins/_all-skins.less'
        }
      }
    },

    // Uglify task info. Compress the js files.
    uglify: {
      options   : {
        mangle          : true,
        preserveComments: 'some'
      },
      production: {
        files: {
          '../../public/js/ungheni.min.js': ['dist/js/adminlte.js']
        }
      }
    },

    // Concatenate JS Files
    concat: {
      options: {
        separator: '\n\n',
        banner   : '/*! Ungheni Today\n'
        + '* ================\n'
        + '* Main JS application file for Ungheni Today. This file\n'
        + '* should be included in all pages. It controls some layout\n'
        + '* options and implements exclusive UT plugins.\n'
        + '*\n'
        + '* @Author  Ion (Nalyk) Calmis\n'
        + '* @Support <https://nalyk.studiotechno.md>\n'
        + '* @Email   <dev.ungheni@gmail.com>\n'
        + '* @version <%= pkg.version %>\n'
        + '* @repository <%= pkg.repository.url %>\n'
        + '* @license MIT <http://opensource.org/licenses/MIT>\n'
        + '*/\n\n'
        + '// Make sure jQuery has been loaded\n'
        + 'if (typeof jQuery === \'undefined\') {\n'
        + 'throw new Error(\'Ungheni Today requires jQuery\')\n'
        + '}\n\n'
      },
      dist   : {
        src : [
          'build/js/BoxRefresh.js',
          'build/js/BoxWidget.js',
          'build/js/ControlSidebar.js',
          'build/js/DirectChat.js',
          'build/js/Layout.js',
          'build/js/PushMenu.js',
          'build/js/TodoList.js',
          'build/js/Tree.js',
          'node_modules/theia-sticky-sidebar/dist/ResizeSensor.min.js',
          'node_modules/theia-sticky-sidebar/dist/theia-sticky-sidebar.min.js',
          'node_modules/jquery-slimscroll/jquery.slimscroll.min.js',
          'node_modules/fastclick/lib/fastclick.js',
          'node_modules/gasparesganga-jquery-loading-overlay/dist/loadingoverlay.min.js'
        ],
        dest: 'dist/js/adminlte.js'
      }
    }

  });

  // Load all grunt tasks

  // LESS Compiler
  grunt.loadNpmTasks('grunt-contrib-less');
  // Watch File Changes
  grunt.loadNpmTasks('grunt-contrib-watch');
  // Compress JS Files
  grunt.loadNpmTasks('grunt-contrib-uglify');
  // Include Files Within HTML
  grunt.loadNpmTasks('grunt-includes');
  // Optimize images
  grunt.loadNpmTasks('grunt-image');
  // Validate JS code
  grunt.loadNpmTasks('grunt-contrib-jshint');
  grunt.loadNpmTasks('grunt-jscs');
  // Delete not needed files
  grunt.loadNpmTasks('grunt-contrib-clean');
  // Lint CSS
  grunt.loadNpmTasks('grunt-contrib-csslint');
  // Lint Bootstrap
  grunt.loadNpmTasks('grunt-bootlint');
  // Concatenate JS files
  grunt.loadNpmTasks('grunt-contrib-concat');
  // Notify
  grunt.loadNpmTasks('grunt-notify');
  // Replace
  grunt.loadNpmTasks('grunt-text-replace');
  // Copy
  grunt.loadNpmTasks('grunt-contrib-copy');

  // Copy task
  grunt.registerTask('files', ['copy:main', 'replace:font']);
  // JS task
  grunt.registerTask('js', ['concat', 'uglify']);
  // CSS Task
  grunt.registerTask('css', ['less:production', 'less:minifiedSkins']);

  // The default task (running 'grunt' in console) is 'watch'
  grunt.registerTask('default', ['watch']);
};
