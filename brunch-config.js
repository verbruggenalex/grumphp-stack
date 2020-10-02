exports.config = {
  optimize: true,
  modules: {
    addSourceUrls: true
  },
  server: {
    run: false
  },
  paths: {
    public: 'web/',
    watched: [
      'src/',
      'web/uikit/src/',
      'web/uikit/custom/'
    ]
  },
  conventions: {
    ignored: [
      /^web\/uikit/,
      /^src\/less\/block/,
      /^src\/less\/common/,
      /^src\/less\/view/
    ],
  },
  files: {
    javascripts: {
      joinTo: {}
    },
    stylesheets: {
      defaultExtension: 'less',
      joinTo: {
        'css/style-user.css': 'src/less/style-user.less',
        'css/style-anonymous.css': 'src/less/style-anonymous.less',
        'css/style-cordova.css': 'src/less/style-cordova.less'
      }
    }
  }
};