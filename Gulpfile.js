var jsfile = [
	'vendor/components/jquery/jquery.js',
	'vendor/components/jqueryui/jquery-ui.js',
	'vendor/timrwood/moment/moment.js',
	'vendor/twbs/bootstrap-sass/assets/javascripts/bootstrap.min.js',
	'vendor/components/jQote2/jquery.jqote2.js',
	'ui/_js/plugins/jquery.getData.js',
	'ui/_js/plugins/jquery.ba-dotimeout.min.js',

	'ui/_js/plugins/jquery.ba-bbq.js',
	'ui/_js/_.js'
];

var jsfile_admin = [
	'vendor/components/toastr/toastr.js',
	'vendor/components/TouchSwipe-Jquery-Plugin/jquery.touchSwipe.min.js',
	'vendor/ivaynberg/select2/dist/js/select2.full.min.js',
	'vendor/components/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
	'vendor/filamentgroup/tablesaw/dist/tablesaw-init.js',
	'vendor/benpickles/peity/jquery.peity.js',
	//'vendor/nnnick/chartjs/dist/Chart.bundle.min.js',
	'vendor/components/daterangepicker/daterangepicker.js',




	'vendor/ichord/caret/dist/jquery.caret.min.js',
	//'vendor/WilliamStam/SearchBox/dist/jquery.searchbox.min.js',
	'vendor/WilliamStam/SearchBox/dist/jquery.searchbox.js',


	'ui/_js/plugins/jquery.highlight.js',

	'ui/app/_js/_.js',
	'ui/app/_js/_lists.js',
	'ui/app/_js/_interactions.js',
	'ui/app/_js/_companies.js',
	'ui/app/_js/_individuals.js',
];


var styleFiles = [
	{
		'file': './ui/_sass/base.scss',
		'path': './ui',
		'filename': 'style.css'
	},
	{
		'file': './ui/front/_sass/base.scss',
		'path': './ui/front',
		'filename': '_style.css'
	},
	{
		'file': './ui/app/_sass/base.scss',
		'path': './ui/app',
		'filename': '_style.css'
	}

];


var javascriptFiles = [
	{
		'files': jsfile,
		'path': './ui',
		'filename': 'javascript.js'
	},

	{
		'files': jsfile_admin,
		'path': './ui/app',
		'filename': '_javascript.js'
	}
]


const sass = require('gulp-sass');
const concat = require('gulp-concat');
const rename = require('gulp-rename');
const merge = require('merge-stream');
var gitCommitMessage = false;


var build = false;

const gulp = require('gulp');
require("time-require");

const duration = require('gulp-duration');


gulp.task('test', function (done) {
	var autoprefixer = (typeof autoprefixer !== 'undefined') ? autoprefixer : require('gulp-autoprefixer');
	var cleanCss = (typeof cleanCss !== 'undefined') ? cleanCss : require('gulp-clean-css');
	var sourcemaps = (typeof sourcemaps !== 'undefined') ? sourcemaps : require('gulp-sourcemaps');
	return gulp.src('./ui/app/_style.css')
		.pipe(autoprefixer({
			browsers: ['last 2 versions'],
			cascade: false
		}))
		.pipe(sourcemaps.init())
		.pipe(cleanCss({
			inline: ['local'],
			rebaseTo: "./",
			specialComments: false,
			//processImport: false,
		}))
		.pipe(sourcemaps.write("."))
		.pipe(gulp.dest('./ui/app'))
});

gulp.task('sass', function (done) {


	if (process.argv.indexOf("--build") != -1) build = true;
	if (process.argv.indexOf("-b") != -1) build = true;


	if (build) {
		var autoprefixer = (typeof autoprefixer !== 'undefined') ? autoprefixer : require('gulp-autoprefixer');
		var cleanCss = (typeof cleanCss !== 'undefined') ? cleanCss : require('gulp-clean-css');
		var sourcemaps = (typeof sourcemaps !== 'undefined') ? sourcemaps : require('gulp-sourcemaps');


	}

	var tasks = styleFiles.map(function (element) {

		var timer = duration(element.path + "/" + element.filename);
		if (build) {
			return gulp.src(element.file)
			//
				.pipe(sass({
					outputStyle: 'compressed',
					includePaths: ['./']
				}))
				.pipe(concat(element.filename, {newLine: ';'}))
				.pipe(gulp.dest(element.path))
				.pipe(autoprefixer({
					browsers: ['last 2 versions'],
					cascade: false
				}))
				.pipe(sourcemaps.init())
				.pipe(cleanCss({
					inline: ['local'],
					rebaseTo: "./",
					specialComments: false,
					//processImport: false,
				}))
				.pipe(sourcemaps.write("."))
				.pipe(timer)
				.pipe(gulp.dest(element.path))
		} else {
			return gulp.src(element.file)
				.pipe(sass({
					outputStyle: 'expanded',
					includePaths: ['./']
				}))
				.pipe(concat(element.filename, {newLine: ';'}))
				.pipe(timer)
				.pipe(gulp.dest(element.path))
		}
	});
	return merge(tasks);

});


gulp.task('javascript', function (done) {

	if (process.argv.indexOf("--build") != -1) build = true;
	if (process.argv.indexOf("-b") != -1) build = true;

	if (build) {
		var sourcemaps = (typeof sourcemaps !== 'undefined') ? sourcemaps : require('gulp-sourcemaps');
		var uglify = (typeof uglify !== 'undefined') ? uglify : require('gulp-uglify');
	}

	var uglify_options = {
		preserveComments: 'license',
		compress:true,

	};


	//build = false;
	var tasks = javascriptFiles.map(function (element) {

		var timer = duration(element.path + "/" + element.filename);
		if (build) {
			return gulp.src(element.files)

				.pipe(concat(element.filename, {newLine: ';'}))
				.pipe(rename(element.filename))
				.pipe(sourcemaps.init())
				.pipe(uglify(uglify_options))
				.pipe(sourcemaps.write("."))
				.pipe(timer)
				.pipe(gulp.dest(element.path));
		} else {
			return gulp.src(element.files)
				.pipe(concat(element.filename, {newLine: ';'}))
				.pipe(rename(element.filename))
				.pipe(timer)
				.pipe(gulp.dest(element.path));
		}
	});
	return merge(tasks);

	done();
});
gulp.task('cleanMaps', function () {

});

gulp.task('js', gulp.parallel('javascript'));
gulp.task('css', gulp.parallel('sass'));


gulp.task('set-build', function (done) {
	build = true;
	if (process.argv.indexOf("--dev") != -1 || process.argv.indexOf("-d") != -1) {
		build = false;
	}
	done();
});

gulp.task('build', gulp.series('set-build', gulp.parallel(['sass', 'javascript']), function (done) {
	done();
}));


gulp.task("composer-update", function (done) {
	var composer = require("gulp-composer");
	composer("self-update", {"self-install": false, "working-dir": './'});
	composer("update", {"self-install": false, "working-dir": './'});
	done();
});


gulp.task('git-commit', function (done) {
	gitCommitMessage = (typeof gitCommitMessage !== 'undefined' && gitCommitMessage != "") ? gitCommitMessage : "gulp commit";

	var d = new Date();
	var prefix = d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) + " " + ("0" + d.getHours()).slice(-2) + ":" + ("0" + d.getMinutes()).slice(-2) + ":" + ("0" + d.getSeconds()).slice(-2);


	gitCommitMessage = prefix + "\n" + gitCommitMessage;

	git = (typeof git !== 'undefined') ? git : require('gulp-git');

	var timer = duration('git-commit');
	return gulp.src('./')
		.pipe(git.commit(gitCommitMessage))
		.pipe(timer);


});
gulp.task('git-push', function (done) {


	git = (typeof git !== 'undefined') ? git : require('gulp-git');

	git.push('remote', 'master', function (err) {
		if (err) {
			throw err;
		} else {
			done();
		}
	});


});
gulp.task('git-diff', function (done) {


	git = (typeof git !== 'undefined') ? git : require('gulp-git');

	git.exec({args: ' diff --stat'}, function (err, stdout) {
		gitCommitMessage = stdout
		if (err) throw err;
		done();
	});


});


gulp.task('update', gulp.series('composer-update', function (done) {
	done();
}));

gulp.task('deploy', gulp.series('build', 'git-diff', 'git-commit', 'git-push', function (done) {
	done();
}));

