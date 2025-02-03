const plugin = require('tailwindcss/plugin');
const postcss = require('postcss');
const postcssJs = require('postcss-js');

const clampGenerator = require('./_src/css-utils/clamp-generator.js');
const tokensToTailwind = require('./_src/css-utils/tokens-to-tailwind.js');

// Raw design tokens
const colorTokens = require('./_src/design-tokens/colors.json');
const fontTokens = require('./_src/design-tokens/fonts.json');
const spacingTokens = require('./_src/design-tokens/spacing.json');
const textSizeTokens = require('./_src/design-tokens/text-sizes.json');
const textLeadingTokens = require('./_src/design-tokens/text-leading.json');
const textTrackingTokens = require('./_src/design-tokens/text-tracking.json');
const textWeightTokens = require('./_src/design-tokens/text-weights.json');

// Process design tokens
const colors = tokensToTailwind(colorTokens.items);
const fontFamily = tokensToTailwind(fontTokens.items);
const fontWeight = tokensToTailwind(textWeightTokens.items);
const fontTracking = tokensToTailwind(textTrackingTokens.items);
const fontSize = tokensToTailwind(clampGenerator(textSizeTokens.items));
const fontLeading = tokensToTailwind(textLeadingTokens.items);
const spacing = tokensToTailwind(clampGenerator(spacingTokens.items));

module.exports = {
  content: [
    './_src/**/*.{html,js,jsx,mdx,njk,json,twig}',
    './_src/design-system/**/*.{twig,json}',
  ],
  presets: [],
  theme: {
    screens: {
      sm: '40em',
      md: '60em',
      lg: '85em'
    },
    colors,
    spacing,
    fontSize,
    fontLeading,
    fontTracking,
    fontFamily,
    fontWeight,
    backgroundColor: ({theme}) => theme('colors'),
    textColor: ({theme}) => theme('colors'),
    margin: ({theme}) => ({
      auto: 'auto',
      ...theme('spacing')
    }),
    padding: ({theme}) => theme('spacing')
  },
  variantOrder: [
    'first',
    'last',
    'odd',
    'even',
    'visited',
    'checked',
    'empty',
    'read-only',
    'group-hover',
    'group-focus',
    'focus-within',
    'hover',
    'focus',
    'focus-visible',
    'active',
    'disabled'
  ],

  // Disables Tailwind's reset etc
  corePlugins: {
    preflight: false
  },
  plugins: [
    // Generates custom property values from tailwind config
    plugin(function ({addComponents, addUtilities, config}) {
      let result = '';

      const currentConfig = config();

      let groups = [
        {key: 'colors', prefix: 'color'},
        {key: 'spacing', prefix: 'space'},
        {key: 'fontSize', prefix: 'size'},
        {key: 'fontFamily', prefix: 'font', property: 'font-family'},
      ];
      const type = [
        {key: 'fontLeading', prefix: 'leading', property: 'line-height'},
        {key: 'fontTracking', prefix: 'tracking', property: 'letter-spacing'},
        {key: 'fontWeight', prefix: 'weight', property: 'font-weight'}
      ];

      type.forEach(({ key, prefix, property }) => {
        const group = currentConfig.theme[key];

        if (!group) {
          return;
        }

        // Generates type helper classes
        Object.keys(group).forEach(key => {
          addUtilities({
            [`.${prefix}-${key}`]: postcssJs.objectify(
              postcss.parse(`${property}: ${group[key]}`)
            )
          });
        });
        
      });

      Object.keys(colors).forEach(key => {
        addUtilities({
          [`.is-style-bg-${key}`]: postcssJs.objectify(
            postcss.parse(`background: ${colors[key]}`)
          )
        });
        addUtilities({
          [`.is-style-text-${key}`]: postcssJs.objectify(
            postcss.parse(`color: ${colors[key]}`)
          )
        });
      });

      groups.concat( type ).forEach(({key, prefix}) => {
        const group = currentConfig.theme[key];

        if (!group) {
          return;
        }

        Object.keys(group).forEach(key => {
          result += `--${prefix}-${key}: ${group[key]};`;
        });
      });

      addComponents({
        ':root': postcssJs.objectify(postcss.parse(result))
      });
    }),

    // Generates custom utility classes
    plugin(function ({addUtilities, config}) {
      const currentConfig = config();
      const customUtilities = [
        {key: 'spacing', prefix: 'flow-space', property: '--flow-space'},
        {key: 'colors', prefix: 'spot-color', property: '--spot-color'}
      ];

      customUtilities.forEach(({key, prefix, property}) => {
        const group = currentConfig.theme[key];

        if (!group) {
          return;
        }

        Object.keys(group).forEach(key => {
          addUtilities({
            [`.${prefix}-${key}`]: postcssJs.objectify(
              postcss.parse(`${property}: ${group[key]}`)
            )
          });
        });
      });
    })
  ]
};
