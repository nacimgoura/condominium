// webpack.config.js
var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where all compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // will create public/build/app.js and public/build/app.css
    .addEntry('app', './assets/js/app.js')

    // convert scss to css
    // .enableSassLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

// create hashed filenames (e.g. app.abc123.css)
// .enableVersioning()
;

// export the final configuration
module.exports = Encore.getWebpackConfig();