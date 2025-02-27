/**
 * Extends Core Gutenberg Blocks with data-attributes
 * 
 * @description Add data-hide-in and data-show-in attributes to every Gutenberg Block. Must be managed at theme level.
 */

(function(wp) {
  const { addFilter } = wp.hooks;
  const { createHigherOrderComponent } = wp.compose;
  const { Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor;
  const { PanelBody, TextControl } = wp.components;

  // Add new attributes to blocks
  const addCustomAttributes = (settings, name) => {
      if (typeof settings.attributes !== 'undefined') {
          settings.attributes = Object.assign({}, settings.attributes, {
              dataHideIn: {
                  type: 'string',
                  default: '0',
              },
              dataShowIn: {
                  type: 'string',
                  default: '0',
              },
          });
      }
      return settings;
  };

  // Add controls for the new attributes
  const withCustomControls = createHigherOrderComponent((BlockEdit) => {
      return (props) => {
          if (props.isSelected) {
              return wp.element.createElement(
                  Fragment,
                  null,
                  wp.element.createElement(BlockEdit, props),
                  wp.element.createElement(
                      InspectorControls,
                      null,
                      wp.element.createElement(
                          PanelBody,
                          { title: 'Regional Visibility', initialOpen: true },
                          wp.element.createElement(TextControl, {
                              label: 'Hide In',
                              value: props.attributes.dataHideIn,
                              onChange: function(newVal) {
                                  props.setAttributes({ dataHideIn: String(newVal) });
                              },
                          }),
                          wp.element.createElement(TextControl, {
                              label: 'Show In',
                              value: props.attributes.dataShowIn,
                              onChange: function(newVal) {
                                  props.setAttributes({ dataShowIn: String(newVal) });
                              },
                          })
                      )
                  )
              );
          }
          return wp.element.createElement(BlockEdit, props);
      };
  }, 'withCustomControls');


  // Apply Gutenberg Filters
  addFilter('blocks.registerBlockType', 'cumminggroup/add-custom-attributes', addCustomAttributes);
  addFilter('editor.BlockEdit', 'cumminggroup/with-custom-controls', withCustomControls);

})(window.wp);
