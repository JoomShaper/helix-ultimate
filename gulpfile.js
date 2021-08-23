const path = require('path');
const { series, parallel, src, dest } = require('gulp');
const del = require('del');
const zip = require('gulp-zip');

/**
 * Configuration object.
 *
 * @var object
 */
const config = {
	srcPath: path.resolve(__dirname),
	buildPath: path.resolve(__dirname, './package/'),
	packageName: 'helix_ultimate_bundle_pkg_2.0.2.zip',
	pluginPackageName: 'helix_ultimate_plugin_pkg_2.0.2.zip',
	templatePackageName: 'helix_ultimate_template_pkg_2.0.2.zip',
	templateFileExtensions: 'xml, json, php, png, scss, js, ico, svg, jpg, eot, ttf, woff, woff2, otf, css',
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
};

function clean() {
	return del([config.buildPath]);
}

function manifestStreamTask() {
	const templatePath = path.resolve(config.srcPath, './templates/shaper_helixultimate');

	return src([templatePath + '/installer.script.php', templatePath + '/installer.xml']).pipe(
		dest(path.resolve(config.buildPath))
	);
}

function templateLanguageStreamTask() {
	const languagePath = path.resolve(config.srcPath, './language/en-GB/**en-GB.tpl_shaper_helixultimate.ini');

	return src([languagePath]).pipe(dest(path.resolve(config.buildPath, './template/')));
}

function templatePluginLanguageStreamTask() {
	const languagePath = path.resolve(
		config.srcPath,
		'./administrator/language/en-GB/**en-GB.plg_system_helixultimate.ini'
	);

	return src([languagePath]).pipe(dest(path.resolve(config.buildPath, './plugins/system/language/')));
}

function templateStreamTask() {
	const templatePath = path.resolve(config.srcPath, './templates/shaper_helixultimate');

	return src([
		templatePath + '/**/*.{' + config.parseExtensions('templateFileExtensions') + '}',
		'!' + templatePath + '/installer.script.php',
		'!' + templatePath + '/installer.xml',
	]).pipe(dest(path.resolve(config.buildPath, './template/')));
}

function pluginStreamTask() {
	return src([
		path.resolve(config.srcPath, './plugins/system/helixultimate') +
			'/**/*.{' +
			config.parseExtensions('pluginFileExtensions') +
			'}',
	]).pipe(dest(path.resolve(config.buildPath, './plugins/system/')));
}

function buildPackage() {
	return src(config.buildPath + '/**')
		.pipe(zip(config.packageName))
		.pipe(dest(config.buildPath));
}

function buildPkgForPlugin() {
	return src(`${config.buildPath}/plugins/system/**`)
		.pipe(zip(config.pluginPackageName))
		.pipe(dest(config.buildPath));
}

function buildPkgForTemplate() {
	return src(`${config.buildPath}/template/**`).pipe(zip(config.templatePackageName)).pipe(dest(config.buildPath));
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

exports.default = series(
	clean,
	parallel(
		manifestStreamTask,
		series(templateLanguageStreamTask, templateStreamTask),
		series(pluginStreamTask, templatePluginLanguageStreamTask)
	),
	parallel(buildPackage, buildPkgForPlugin, buildPkgForTemplate),
	clear
);
