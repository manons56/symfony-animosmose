const Encore = require('@symfony/webpack-encore');

// Configuration de l'environnement si pas déjà configuré
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Dossier de sortie pour les fichiers compilés
    .setOutputPath('public/build/')
    .setPublicPath('/build')

    // Entrée SCSS globale et par page
    .addEntry('app', './assets/app.js')
    .addEntry('home', './assets/styles/pages/home.scss')
    .addEntry('contact', './assets/styles/pages/contact.scss')
    .addEntry('pension', './assets/styles/pages/pension.scss')
    .addEntry('stable', './assets/styles/pages/stable.scss')
    .addEntry('breeding', './assets/styles/pages/breeding.scss')
    .addEntry('product', './assets/styles/pages/product.scss')


    // Sépare les fichiers pour optimiser
    .splitEntryChunks()

    // Fichier runtime séparé
    .enableSingleRuntimeChunk()

    // Nettoie le dossier avant chaque build
    .cleanupOutputBeforeBuild()

    // Source maps en développement
    .enableSourceMaps(!Encore.isProduction())

    // Versioning pour la prod (hash dans les fichiers)
    .enableVersioning(Encore.isProduction())

    // Configuration Babel pour compatibilité navigateurs
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })


    // Support SCSS
    .enableSassLoader()

    // pour que les images de asset aille dans public/build
    .copyFiles({
        from: 'assets/img', // dossier source
        to: 'images/[name].[hash:8].[ext]' //destination
    })

    .copyFiles({
        from: './assets/videos',
        to: 'videos/[path][name].[hash:8].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
