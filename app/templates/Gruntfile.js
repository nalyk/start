// Gruntfile.js
module.exports = grunt => {
        // Load all grunt tasks matching the ['grunt-*', '@*/grunt-*'] patterns
        require('load-grunt-tasks')(grunt);

        grunt.initConfig({
        copy: {
            main: {
                files: [
                        // jQuery
                        {expand: true, cwd: 'node_modules/jquery/dist', src: ['**'], dest: '../../public/js/', filter: 'isFile'},

                        // popper.js
                        {expand: true, cwd: 'node_modules/popper.js/dist/umd', src: ['**'], dest: '../../public/js/', filter: 'isFile'},

                        // Bootstrap
                        {expand: true, cwd: 'node_modules/bootstrap/dist/css', src: ['**'], dest: '../../public/css/', filter: 'isFile'},
                        {expand: true, cwd: 'node_modules/bootstrap/dist/js', src: ['**'], dest: '../../public/js/', filter: 'isFile'},
                ]
            }
        },
    });
};
