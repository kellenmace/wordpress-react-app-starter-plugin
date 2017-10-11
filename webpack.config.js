const commonConfig = require( './webpack/webpack.common' );
const webpackMerge = require( 'webpack-merge' );

// No need for addons at this time.
module.exports = ( env ) => {
	const envConfig = require( `./webpack/webpack.${env.env}.js` );
	const mergedConfig = webpackMerge( commonConfig, envConfig );

	return mergedConfig;
};
