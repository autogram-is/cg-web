const markdownIt = require('markdown-it');
const eleventyPluginTwig = require("@factorial/eleventy-plugin-twig");
const syntaxHighlight = require("@11ty/eleventy-plugin-syntaxhighlight");
const twig = require('twig');
const slugify = require('slugify');

// Creates a global variable for the current __dirname to make including and
// working with files in the pattern library a little easier
global.__basedir = __dirname;

module.exports = config => {
  // Tell 11ty to use the .eleventyignore and ignore .gitignore file
  config.setUseGitIgnore(false);

  // Add syntax highlighting
  config.addPlugin(syntaxHighlight);

  config.addFilter('md', require('./_src/filters/md.js'));

  // Options for the `markdown-it` library
  const markdownItOptions = {
    html: true
  };

  const markdownLib = markdownIt(markdownItOptions);

  config.setLibrary('md', markdownLib);

  // “smart” markdown filter that won’t insert `p` on single lines
  twig.extendFilter("md", require('./_src/filters/md.js') );

  // Default Markdown behavior filter
  const md = new markdownIt({
    html: true
  });
  twig.extendFilter("markdown", (content) => content && md.render( content ) );

    // Add missing Twig filters
  twig.extendFilter("slug", ( value ) => slugify( value.toLowerCase() ) );
  twig.extendFilter("safe", ( value ) => value );
  twig.extendFilter("escape", ( value ) => value.replace(/./gm, ( val ) => (val.match(/[a-z0-9\s]+/i)) ? val : "&#" + val.charCodeAt(0) + ";" ));
  twig.extendFilter("longdate", ( value ) => new Date( value ).toLocaleString( 'en-US', { year: "numeric", month: "long", day: "numeric" }) );


  // Allow use of twig templates
  config.addPlugin(eleventyPluginTwig, { 
    dir: {
      input: '_src',
      output: '_site'
    }
  });

  // Pass-through
  config.addPassthroughCopy('./_src/fonts', 'fonts');
  config.addPassthroughCopy('./_src/images', 'images');

  return {
    markdownTemplateEngine: 'njk',
    dataTemplateEngine: 'njk',
    htmlTemplateEngine: 'njk',
    dir: {
      input: '_src',
      output: '_site'
    }
  };
};
