const Encore = require('@symfony/webpack-encore');
const path = require("path");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('../asset/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/asset/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    .addAliases({
        '~': path.resolve(__dirname, 'assets'),
    })

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('app_bottom', './assets/app_bottom.js')
    .addEntry('balance_top_up', './assets/public/payment/balance_top_up.js')
    .addEntry('payment_await_confirmation', './assets/public/payment/payment_await_confirmation.js')
    .addEntry('feature_listing', './assets/public/feature_listing.js')
    .addEntry('listing_edit', './assets/public/listing_edit.js')
    .addEntry('listing_list', './assets/public/listing_list.js')
    .addEntry('listing_list_map', './assets/public/listing_list_map.js')
    .addEntry('listing_reject', './assets/public/listing_reject')
    .addEntry('listing_show', './assets/public/listing_show.js')
    .addEntry('user_listing_list', './assets/public/user_listing_list.js')
    .addEntry('user_message', './assets/public/user_message.js')

    .addEntry('app_admin', './assets/admin/app_admin.js')
    .addEntry('app_admin_bottom', './assets/admin/app_admin_bottom.js')
    .addEntry('admin_category_edit', './assets/admin/page/category/admin_category_edit.js')
    .addEntry('admin_category_list', './assets/admin/page/category/admin_category_list.js')
    .addEntry('admin_custom_field_edit', './assets/admin/page/custom_field/admin_custom_field_edit.js')
    .addEntry('admin_custom_field_list', './assets/admin/page/custom_field/admin_custom_field_list.js')
    .addEntry('admin_custom_field_option', './assets/admin/page/custom_field/admin_custom_field_option.js')
    .addEntry('admin_featured_package_edit', './assets/admin/page/featured_package/admin_featured_package_edit.js')
    .addEntry('admin_listing_activate', './assets/admin/page/listing/admin_listing_activate.js')
    .addEntry('admin_listing_edit', './assets/admin/page/listing/admin_listing_edit.js')
    .addEntry('admin_listing_edit_advanced', './assets/admin/page/listing/admin_listing_edit_advanced.js')
    .addEntry('admin_listing_execute_on_filtered', './assets/admin/page/listing/admin_listing_execute_on_filtered.js')
    .addEntry('admin_police_log_listing', './assets/admin/page/secondary/admin_police_log_listing.js')
    .addEntry('admin_police_log_user_message', './assets/admin/page/secondary/admin_police_log_user_message.js')
    .addEntry('admin_listing_search', './assets/admin/page/listing/admin_listing_search.js')
    .addEntry('admin_listing_show', './assets/admin/page/listing/admin_listing_show.js')
    .addEntry('admin_page_edit', './assets/admin/page/secondary/admin_page_edit.js')
    .addEntry('admin_upgrade', './assets/admin/page/secondary/admin_upgrade.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    // .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    .configureDevServerOptions(options => {
        options.https = {
            key: 'docker/php/ssl/localhost.key',
            cert: 'docker/php/ssl/localhost.crt',
        }
    })

    .configureImageRule({
        type: 'asset',
        //maxSize: 4 * 1024, // 4 kb - the default is 8kb
    })

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
    .autoProvideVariables({
        // $: 'jquery',
        // jQuery: 'jquery',
        // 'window.jQuery': 'jquery',
        // 'Routing': 'Routing',
        // 'Translation': 'Translation',
    })
;

module.exports = Encore.getWebpackConfig();
