const globalMixins = require('./_src/css-utils/global-mixins.js');

module.exports = {
  plugins: [
    require('postcss-import-ext-glob'),
    require('postcss-import'),
    require('tailwindcss'),
    require('cssnano'),
    require('postcss-nesting')({
      edition: '2024-02'
    }),
    require('postcss-mixins')({
      mixins: globalMixins
    })
  ]
};
