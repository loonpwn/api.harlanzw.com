const webpack = require('webpack');
const Dotenv = require('dotenv-webpack');
const GitRevisionPlugin = require('git-revision-webpack-plugin');

// load our .env file
const dotenv = require('dotenv');
dotenv.config();
const gitVars = new GitRevisionPlugin();

module.exports = {
    plugins: [
        new Dotenv(),
        new webpack.EnvironmentPlugin([
            'GA_ID',
            'WP_ENV',
        ]),
        new webpack.DefinePlugin({
            'COMMITHASH': JSON.stringify(gitVars.commithash()),
        }),
    ],
};
