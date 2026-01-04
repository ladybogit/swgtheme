const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, argv) => {
	const isDevelopment = argv.mode === 'development';

	return {
		entry: {
			'theme-bundle': './js/theme-bundle.js',
		},
		output: {
			path: path.resolve(__dirname, 'dist'),
			filename: '[name].js',
			clean: true,
		},
		module: {
			rules: [
				{
					test: /\.js$/,
					exclude: /node_modules/,
					use: {
						loader: 'babel-loader',
						options: {
							presets: ['@babel/preset-env'],
						},
					},
				},
				{
					test: /\.css$/,
					use: [
						MiniCssExtractPlugin.loader,
						'css-loader',
					],
				},
			],
		},
		plugins: [
			new MiniCssExtractPlugin({
				filename: '[name].css',
			}),
		],
		optimization: {
			minimize: !isDevelopment,
			minimizer: [
				new TerserPlugin({
					terserOptions: {
						format: {
							comments: false,
						},
					},
					extractComments: false,
				}),
				new CssMinimizerPlugin(),
			],
		},
		devtool: isDevelopment ? 'source-map' : false,
		performance: {
			hints: isDevelopment ? false : 'warning',
			maxEntrypointSize: 512000,
			maxAssetSize: 512000,
		},
		watchOptions: {
			ignored: /node_modules/,
			aggregateTimeout: 300,
		},
	};
};
