const path = require( 'path' );
const ProgressBar = require( 'progress-bar-webpack-plugin' );
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const config = {

	entry: './app/index.js',
	output: {
		filename: 'scripts.js',
		path: path.resolve( __dirname, '../', 'dist' )
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			},
			{
				test: /\.scss/,
				use: ExtractTextPlugin.extract({
					fallback: 'style-loader',
					use: [
						{
							loader: 'css-loader',
							options: {
								sourceMap: true
							}
						},
						{
							loader: 'sass-loader',
							options: {
								sourceMap: true
							}
						}
					]
				})
			}
		]
	},
	plugins: [
		new ProgressBar()
	]
};

module.exports = config;
