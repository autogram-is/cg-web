/**
 * Global utility mixins
 */
const globalMixins = {
  visuallyhidden: {
    border: 0,
    clip: "rect(0 0 0 0)",
    height: 0,
    margin: 0,
    overflow: "hidden",
    padding: 0,
    position: "absolute",
    width: "1px",
    "white-space": "nowrap"
  },
  hatching: {
    background: "url('/images/hatch.svg') 0 0 no-repeat",
    height: "var(--space-s)",
    backgroundRepeat: "repeat-x",
    backgroundSize: "90rem"
  },
  invert: {
    color: "var(--color-steel-highlight)",
    fill: "var(--color-steel-highlight)",
    borderColor: "var(--color-steel-midtone)",
    'a:hover': {
      color: "var(--color-light)"
    }
  }
};

module.exports = globalMixins;