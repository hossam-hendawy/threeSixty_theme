const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require("html-webpack-plugin");
const {CleanWebpackPlugin} = require("clean-webpack-plugin");
const {WebpackManifestPlugin} = require("webpack-manifest-plugin");
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;

class DoNothingPlugin {
  constructor(options) {
    this.options = options;
  }

  apply(compiler) {
  }
}

module.exports = function (env, argv) {
  console.log(argv, __dirname);
  return {
    entry: env.isAdmin ? './src/admin/index.js' : './src/index.js',
    output: {
      filename: env.isAdmin ? 'admin.js' : `main${env.asBuild ? '' : '.[hash]'}.js`,
      chunkFilename: '[name].[chunkhash].chunk.js',
      path: path.resolve(__dirname, '../assets/'),
      publicPath: argv.mode === 'production' || env.asBuild ? '/' + (env && env.subDomain || '') + 'wp-content/themes/threeSixty_theme/assets/' : './',
    },
    // mode:argv.mode||'development',
    resolve: {
      roots: [path.resolve(__dirname)]
    },
    watch: false,
    module: {
      rules: [
        {
          test: /\.html$/i,
          use: argv.mode === 'production' || env.asBuild ? 'ignore-loader' : [
            {
              loader: 'file-loader',
              options: {
                name: function (fileName) {
                  const parts = fileName.replace(/\\/g, '/').split('/');
                  const len = parts.length;
                  if (parts[len - 2] === 'html') {
                    return parts[len - 1];
                  }
                  return `${parts[len - 2]}.html`;
                },
              },
            },
            'extract-loader',
            {
              loader: 'html-loader', options: {
                esModule: false,
                sources: {
                  list: [
                    {
                      // Tag name
                      tag: 'img',
                      // Attribute name
                      attribute: 'src',
                      // Type of processing, can be `src` or `scrset`
                      type: 'src',
                    },
                    {
                      // Tag name
                      tag: 'img',
                      // Attribute name
                      attribute: 'srcset',
                      // Type of processing, can be `src` or `scrset`
                      type: 'srcset',
                    },
                    {
                      tag: 'img',
                      attribute: 'data-src',
                      type: 'src',
                    },
                    {
                      tag: 'img',
                      attribute: 'data-srcset',
                      type: 'srcset',
                    }, {
                      tag: 'video',
                      attribute: 'data-srcset',
                      type: 'srcset',
                    }, {
                      tag: 'video',
                      attribute: 'srcset',
                      type: 'srcset',
                    }, {
                      tag: 'video',
                      attribute: 'src',
                      type: 'src',
                    }, {
                      tag: 'video',
                      attribute: 'data-src',
                      type: 'src',
                    }, {
                      tag: 'source',
                      attribute: 'data-src',
                      type: 'src',
                    }, {
                      tag: 'source',
                      attribute: 'src',
                      type: 'src',
                    }, {
                      tag: 'image',
                      attribute: 'xlink:href',
                      type: 'src',
                    },
                  ],
                  urlFilter: (attribute, value, resourcePath) => {
                    return !/style.css$/.test(value) && !/main.css$/.test(value) && !/main.js$/.test(value);
                  },
                },
              },
            }],
        },
        {
          test: /\.jsx?$/,
          exclude: /(node_modules|bower_components)/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: [['@babel/env', {
                "useBuiltIns": "usage",
                "corejs": 3
              }]],
              plugins: [
                '@babel/plugin-proposal-class-properties',
                '@babel/plugin-proposal-object-rest-spread',
                '@babel/plugin-proposal-export-default-from',
                '@babel/plugin-proposal-export-namespace-from',
              ],
            },
          },
        },
        {
          test: /\.s?css$/,
          use: [
            argv.mode === 'production' || env.asBuild ? MiniCssExtractPlugin.loader : 'style-loader',
            {
              loader: 'css-loader',
              options: {importLoaders: 1, sourceMap: true}
            },
            {loader: 'postcss-loader', options: {}},
            'resolve-url-loader',
            {loader: 'sass-loader', options: {sourceMap: true}},
          ],
        },
        {
          test: /\.(woff(2)?|ttf|eot|otf)(\?v=\d+\.\d+\.\d+)?$/,
          use: [{
            loader: 'file-loader',
            options: {
              name: '[folder]/[name].[ext]',
            },
          }],
        },
        {
          test: /\.(png|svg|jpg|gif)$/,
          use: [{
            loader: 'file-loader',
            options: {
              publicPath: function (url) {
                return argv.mode === 'production' || env.asBuild ? '/' + (env && env.subDomain || '') + 'wp-content/themes/threeSixty_theme/assets/' + url : './' + url;
              },
              name: '[path][name].[ext]',
              context: path.resolve(__dirname, './src'),
            },
          }],
        },
      ],
    },

    optimization: {
      sideEffects: false,
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: env.isAdmin ? 'admin.css' : `main${env.asBuild ? '' : '.[hash]'}.css`,
        chunkFilename: '[name].[chunkhash].chunk-style.css',
      }),
      new webpack.DefinePlugin({
        'NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
        'NODE_ENV_ADMIN': env && !!env.isAdmin,
      }),
      new HtmlWebpackPlugin({
        title: 'My App',
        filename: '_.html'
      }),
      // (env.isAdmin) ? new DoNothingPlugin() : new CleanWebpackPlugin(),
      (env.isAdmin || env.asBuild) ? new DoNothingPlugin() : new WebpackManifestPlugin({
        filter: (props) => props.isInitial,
      }),
      // new BundleAnalyzerPlugin()
    ],
    externals: {
      jquery: 'jQuery'
    },
    devtool: argv.mode === 'production' ? 'source-map' : 'eval',
    devServer: {
      contentBase: path.resolve(__dirname, '../assets/'),
      writeToDisk: true,
      openPage: '',
      // port: 8080,
      // hot: true,
      // historyApiFallback: true,
    },
  };
};
