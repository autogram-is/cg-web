/*
  THEME CONFIG

  In here, we have some enum-style keys that refer to colors in context. These
  keys are then used to inform a relationship between context and design tokens.

  The structure for a color design token reference is ${group}-${item} and should
  be lowercase, so if you wanted to use the core primary color, you would use
  'core-primary' because “Core” is the group and “Primary” is the item
*/
module.exports = {
  colorKeys: {
    MODE: 'mode', // DO NOT DELETE
    ACCENT: 'accent',
    ACTION_BG: 'action-bg',
    ACTION_STROKE: 'action-stroke',
    ACTION_TEXT: 'action-text',
    BLOCK_LINK_BG: 'block-link-bg',
    BLOCK_LINK_ACTIVE_BG: 'block-link-active-bg',
    BLOCK_LINK_ACTIVE_DECOR: 'block-link-active-decor',
    BLOCK_LINK_HOVER_BG: 'block-link-hover-bg',
    BLOCK_LINK_TEXT: 'block-link-text',
    CORE_BG: 'core-bg',
    CORE_TEXT: 'core-text',
    ERROR_BG: 'error-bg',
    ERROR_TEXT: 'error-text',
    FIELD_STROKE: 'field-stroke',
    FOCUS_RING: 'focus-ring',
    INFO_BG: 'info-bg',
    INFO_TEXT: 'info-text',
    MID_BG: 'mid-bg',
    MID_TEXT: 'mid-text',
    POD_BG: 'pod-bg',
    REV_BG: 'rev-bg',
    REV_TEXT: 'rev-text',
    ROW_HOVER: 'row-hover',
    SHADE_BG: 'shade-bg',
    SUCCESS_BG: 'success-bg',
    SUCCESS_TEXT: 'success-text',
    STROKE: 'stroke'
  },
  getDark() {
    return {
      MODE: 'dark', // DO NOT DELETE
      ACTION_BG: 'shades-dark-highlight',
      ACCENT: 'core-secondary',
      ACTION_STROKE: 'core-primary',
      ACTION_TEXT: 'shades-light-shade',
      BLOCK_LINK_BG: 'shades-dark-highlight',
      BLOCK_LINK_ACTIVE_BG: 'shades-dark',
      BLOCK_LINK_ACTIVE_DECOR: 'core-primary-highlight',
      BLOCK_LINK_HOVER_BG: 'shades-mid',
      BLOCK_LINK_TEXT: 'shades-light',
      CORE_BG: 'shades-dark',
      CORE_TEXT: 'shades-light',
      ERROR_BG: 'state-error-dim',
      ERROR_TEXT: 'state-error-highlight',
      FIELD_STROKE: 'core-secondary-highlight',
      FOCUS_RING: 'core-secondary-highlight',
      INFO_BG: 'core-mid',
      INFO_TEXT: 'shades-light',
      MID_BG: 'shades-dark-highlight',
      MID_TEXT: 'shades-gray',
      POD_BG: 'shades-dark',
      REV_BG: 'shades-mid',
      REV_TEXT: 'shades-light-highlight',
      ROW_HOVER: 'shades-dark-shade',
      SHADE_BG: 'shades-dark-shade',
      SUCCESS_BG: 'state-success-dim',
      SUCCESS_TEXT: 'state-success-highlight',
      STROKE: 'shades-gray'
    };
  },
  getLight() {
    return {
      MODE: 'light', // DO NOT DELETE
      ACCENT: 'core-secondary-shade',
      ACTION_BG: 'shades-gray-highlight',
      ACTION_STROKE: 'core-primary',
      ACTION_TEXT: 'shades-dark-highlight',
      BLOCK_LINK_BG: 'shades-gray-highlight',
      BLOCK_LINK_ACTIVE_BG: 'shades-light',
      BLOCK_LINK_ACTIVE_DECOR: 'core-primary',
      BLOCK_LINK_HOVER_BG: 'shades-gray',
      BLOCK_LINK_TEXT: 'shades-dark-highlight',
      CORE_BG: 'shades-light-highlight',
      CORE_TEXT: 'shades-dark',
      MID_BG: 'shades-gray-highlight',
      MID_TEXT: 'shades-mid',
      ERROR_BG: 'state-error-highlight',
      ERROR_TEXT: 'state-error-shade',
      FIELD_STROKE: 'core-secondary-shade',
      FOCUS_RING: 'core-secondary-shade',
      INFO_BG: 'core-secondary',
      INFO_TEXT: 'shades-dark',
      POD_BG: 'core-secondary-highlight',
      REV_BG: 'shades-dark-shade',
      REV_TEXT: 'shades-light',
      ROW_HOVER: 'core-primary-highlight',
      SHADE_BG: 'shades-gray',
      SUCCESS_BG: 'state-success-highlight',
      SUCCESS_TEXT: 'state-success-dim',
      STROKE: 'shades-gray'
    };
  },
  generate() {
    return [
      {
        name: 'default',
        tokens: this.getLight()
      },
      {
        name: 'dark',
        key: 'prefers-color-scheme',
        value: 'dark',
        tokens: this.getDark()
      },
      {
        name: 'dark-toggle',
        key: 'prefix',
        value: '[data-user-theme="dark"]',
        tokens: this.getDark()
      },
      {
        name: 'light-toggle',
        key: 'prefix',
        value: '[data-user-theme="light"]',
        tokens: this.getLight()
      }
    ];
  },
  get backgroundUtilities() {
    return Object.keys(this.colorKeys)
      .filter(key => key.includes('_BG'))
      .map(key => ({
        utilityClass: `bg-${this.colorKeys[key]}`,
        sassMixin: `@include apply-utility('bg', '${this.colorKeys[key]}')`,
        sassFunction: `get-utility-value('bg', '${this.colorKeys[key]}')`
      }));
  },
  get textUtilities() {
    return Object.keys(this.colorKeys)
      .filter(key => key.includes('_TEXT'))
      .map(key => ({
        utilityClass: `color-${this.colorKeys[key]}`,
        sassMixin: `@include apply-utility('color', '${this.colorKeys[key]}')`,
        sassFunction: `get-utility-value('color', '${this.colorKeys[key]}')`
      }));
  }
};
