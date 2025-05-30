const colors = require('../../design-tokens/colors.json');
const fonts = require('../../design-tokens/fonts.json');
const spacing = require('../../design-tokens/spacing.json');
const textSizes = require('../../design-tokens/text-sizes.json');
const textLeading = require('../../design-tokens/text-leading.json');
const textTracking = require('../../design-tokens/text-tracking.json');
const textWeights = require('../../design-tokens/text-weights.json');

module.exports = () => {
  return {
    colors: colors,
    textSizes: textSizes,
    textLeading: textLeading,
    textTracking: textTracking,
    textWeights: textWeights,
    spacing: spacing,
    fonts: fonts
  };
};
