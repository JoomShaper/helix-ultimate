const path = require('path');
const { series, parallel, src, dest, relo } = require('gulp');
const del = require('del');
const zip = require('gulp-zip');
const minifyCss = require('gulp-clean-css');
const uglify = require('gulp-uglify-es').default;

/**
 * Configuration object.
 *
 * @var object
 */
const config = {
    srcPath: path.resolve(__dirname),
    buildPath: path.resolve(__dirname, './package/'),
    qsPath: path.resolve(__dirname, './package/helix_ultimate_quickstart/'),
    qsPackageName: 'helixultimate_quickstart_j4_2.1.0.zip',
    packageName: 'helixultimate_template_v2.1.0.zip',
    pluginPackageName: 'plg_system_helixultimate_v2.1.0.zip',
    templateFileExtensions: 'xml, json, php, png, scss, js, ico, svg, jpg, eot, ttf, woff, woff2, otf, css, html',
    pluginFileExtensions: function () {
        return this.templateFileExtensions + ', ini';
    },
    toArray: function (key) {
        if (typeof this[key] === 'string') return this[key].split(',').map(v => v.trim());
        else if (typeof this[key] === 'function')
            return this[key]()
                .split(',')
                .map(v => v.trim());
    },
    parseExtensions: function (key) {
        if (typeof this[key] === 'string') return this[key].replace(/\s+/g, '');
        else if (typeof this[key] === 'function') return this[key]().replace(/\s+/g, '');
    },
    destOptions: {
        mode: 0644,
        dirMode: 0755,
    },
};

function exportSqlDump(done) {
    mysqldump({
        connection: {
            host: 'localhost',
            user: 'root',
            password: 'root',
            database: 'helix_v2_j4',
        },
        dumpToFile: path.resolve(`${config.qsPath}/installation/custom.sql`),
    });
    done();
}

function clean() {
    return del([config.buildPath]);
}

function manifestStreamTask() {
    const templatePath = path.resolve(config.srcPath, './templates/shaper_helixultimate');

    return src([templatePath + '/installer.script.php', templatePath + '/installer.xml']).pipe(
        dest(path.resolve(config.buildPath), config.destOptions)
    );
}

function templateLanguageStreamTask() {
    const languagePath = path.resolve(config.srcPath, './language/en-GB/**en-GB.tpl_shaper_helixultimate.ini');

    return src([languagePath]).pipe(dest(path.resolve(config.buildPath, './template/'), config.destOptions));
}

function templatePluginLanguageStreamTask() {
    const languagePath = path.resolve(
        config.srcPath,
        './administrator/language/en-GB/**en-GB.plg_system_helixultimate.ini'
    );

    return src([languagePath]).pipe(
        dest(path.resolve(config.buildPath, './plugins/system/language/'), config.destOptions)
    );
}

function templateStreamTask() {
    const templatePath = path.resolve(config.srcPath, './templates/shaper_helixultimate');

    return src([
        templatePath + '/**/*.{' + config.parseExtensions('templateFileExtensions') + '}',
        '!' + templatePath + '/installer.script.php',
        '!' + templatePath + '/installer.xml',
    ]).pipe(dest(path.resolve(config.buildPath, './template/'), config.destOptions));
}

function pluginStreamTask() {
    return src([
        path.resolve(config.srcPath, './plugins/system/helixultimate') +
            '/**/*.{' +
            config.parseExtensions('pluginFileExtensions') +
            '}',
    ]).pipe(dest(path.resolve(config.buildPath, './plugins/system/'), config.destOptions));
}

function buildPackage() {
    return src(config.buildPath + '/**')
        .pipe(zip(config.packageName))
        .pipe(dest(config.buildPath, config.destOptions));
}

function minifyPluginCss() {
    return src([
        `${config.buildPath}/plugins/system/assets/css/*.css`,
        `!${config.buildPath}/plugins/system/assets/css/bootstrap.min.css`,
    ])
        .pipe(minifyCss())
        .pipe(dest(`${config.buildPath}/plugins/system/assets/css`, config.destOptions));
}

function minifyPluginAdminCss() {
    return src([
        `${config.buildPath}/plugins/system/assets/css/admin/*.css`,
        `!${config.buildPath}/plugins/system/assets/css/admin/*.min.css`,
    ])
        .pipe(minifyCss())
        .pipe(dest(`${config.buildPath}/plugins/system/assets/css/admin`, config.destOptions));
}

function minifyPluginAdminJs() {
    return src([
        `${config.buildPath}/plugins/system/assets/js/admin/*.js`,
        `!${config.buildPath}/plugins/system/assets/js/admin/*.min.js`,
    ])
        .pipe(uglify())
        .pipe(dest(`${config.buildPath}/plugins/system/assets/js/admin`, config.destOptions));
}

function buildPkgForPlugin() {
    return src(`${config.buildPath}/plugins/system/**`)
        .pipe(zip(config.pluginPackageName))
        .pipe(dest(config.buildPath, config.destOptions));
}

function clear() {
    return del(
        [
            `${config.buildPath}/plugins`,
            `${config.buildPath}/template`,
            `${config.buildPath}/installer.script.php`,
            `${config.buildPath}/installer.xml`,
        ],
        { force: true }
    );
}

function QSFiles() {
    const files = ['cache/index.html', 'htaccess.txt', 'index.php', 'LICENSE.txt', 'README.txt', 'web.config.txt'].map(
        file => `${config.srcPath}/${file}`
    );

    return src(files).pipe(dest(config.qsPath));
}

function QSDirectories(done) {
    const directories = [
        'administrator',
        'api',
        'cli',
        'cache',
        'components',
        'installation',
        'images',
        'includes',
        'language',
        'layouts',
        'libraries',
        'media',
        'modules',
        'plugins',
        'templates',
        'tmp',
    ];

    const tasks = [];

    for (const dir of directories) {
        const task = taskDone => {
            src(`${config.srcPath}/${dir}/**/*.*`).pipe(dest(`${config.qsPath}/${dir}/`));
            taskDone();
        };
        tasks.push(task);
    }

    return series(...tasks, seriesDone => {
        seriesDone();
        done();
    })();
}

function packTheQS() {
    return src(`${config.qsPath}/**/*.*`)
        .pipe(zip(config.qsPackageName))
        .pipe(dest(config.buildPath, config.destOptions));
}

exports.default = series(
    clean,
    parallel(
        manifestStreamTask,
        series(templateLanguageStreamTask, templateStreamTask),
        series(pluginStreamTask, templatePluginLanguageStreamTask)
    ),
    parallel(minifyPluginCss, minifyPluginAdminCss, minifyPluginAdminJs),
    parallel(buildPackage, buildPkgForPlugin),
    clear
    // series(parallel(QSDirectories, QSFiles), packTheQS)
);
