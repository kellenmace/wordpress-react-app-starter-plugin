const webpack = require( 'webpack' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const ExtractTextPlugin = require('extract-text-webpack-plugin');

const config = {
	devtool: 'eval-source-map',
	devServer: {
		historyApiFallback: true,
		hot: true
	},
	plugins: [
		// Extract the CSS file.
		new ExtractTextPlugin( 'style.css' ),

		// In WordPress environments, Browsersync is a better option than webpack-dev-server.
		// See: https://deliciousbrains.com/develop-wordpress-plugin-webpack-3-react/
		new BrowserSyncPlugin({
			open: false,
			injectChanges: true,
			host: 'localhost',
			port: '3000',
			proxy: 'wp-react.dev'
		})
	]
};

module.exports = config;
