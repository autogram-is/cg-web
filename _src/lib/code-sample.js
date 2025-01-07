/* global __basedir */

const chalk = require('chalk');
const Twig = require('twig');
const fs = require('fs');
const path = require('path');
const prettier = require('prettier');

// Set up the chalk warning and error state
const warning = chalk.black.bgYellow;
const error = chalk.black.bgRed;

class CodeSample {
  constructor(samplePath, permalinkPrefix = '', previewPrefix = '') {
    // The folder path from base where the code samples live
    this.samplePath = samplePath;

    // Remove trailing slash from the permalink/preview prefix
    this.permalinkPrefix = permalinkPrefix.replace(/\/$/, '');
    this.previewPrefix = previewPrefix.replace(/\/$/, '');

    // For storing processed items for speedier builds
    this.processedItems = [];
  }

  // Grabs all codeSamples that it can find at the root level then builds up a dataset,
  // rendered markup, view markup and docs. Lastly, it finds any variants and makes
  // those part of the codeSample, too
  get items() {
    // @ts-ignore

    // If the items have already been processed, it's an immediate return
    if (this.processedItems.length) {
      return this.processedItems;
    }

    const basePath = path.join(__basedir, '_src', this.samplePath);

    // Gets codeSample paths, excluding hidden files/folders and files
    const getCodeSamplePaths = refPath => {
      return fs
        .readdirSync(refPath)
        .filter(item => !/(^|\/)\.[^/.]/g.test(item)) // Hidden
        .filter(item => !/[^\\]*\.(\w+)$/.test(item)); // detect file
    };

    // Parses out the codeSample name from the last segment in its path
    const getCodeSampleName = codeSamplePath => {
      const pathParts = codeSamplePath.split('/').filter(x => x.length);
      return pathParts[pathParts.length - 1];
    };

    const codeSamples = getCodeSamplePaths(basePath);

    // For creating a result collection
    const result = [];

    // This is used for both codeSamples and variants to grab markup, data and docs
    const buildCodeSample = (
      codeSamplePath,
      codeSampleName,
      parentPath = null,
      parentName = null,
      contextData = null
    ) => {
      const response = {};

      // Attempt to load markup from the pass codeSamplePath and codeSampleName first,
      // but if that can’t be found, attempt to load from the parent instead, if
      // its details have been passed in
      if (fs.existsSync(path.resolve(codeSamplePath, `${codeSampleName}.twig`))) {
        response.markup = fs.readFileSync(
          path.resolve(codeSamplePath, `${codeSampleName}.twig`),
          'utf8'
        );
      } else {
        if (parentPath !== null && parentName !== null) {
          if (fs.existsSync(path.resolve(parentPath, `${parentName}.twig`))) {
            response.markup = fs.readFileSync(
              path.resolve(parentPath, `${parentName}.twig`),
              'utf8'
            );
          }
        }
      }

      // All markup avenues exhausted so time to bail out
      if (!response?.markup?.length) {
        console.log(
          warning(
            `Markup file, ${codeSampleName}.twig wasn’t found, so this codeSample (${codeSamplePath}) can’t be built up`
          )
        );
        return null;
      }

      // If specific context data has been passed, we prioritise that
      if (contextData) {
        response.data = contextData.context ? contextData : {context: contextData};
      }
      // If not, we look for a data file
      else if (fs.existsSync(path.resolve(codeSamplePath, `${codeSampleName}.json`))) {
        response.data = buildCodeSampleData(
          fs.readFileSync(path.resolve(codeSamplePath, `${codeSampleName}.json`), 'utf8'),
          path.resolve(codeSamplePath, `${codeSampleName}.json`)
        );
      }

      // Render the pattern with Twig and then run it through
      // prettier so format it correctly to make copy/paste easier
      let markup = Twig.twig({
          data: response.markup
      });

      response.rendered = prettier
        .format(
          markup.render( response?.data?.context || {} ),
          {
            useTabs: false,
            tabWidth: 2,
            parser: 'html'
          }
        )
        .replace(/^\s*\n/gm, ''); // Gets rid of blank lines (https://stackoverflow.com/q/16369642)

      if (fs.existsSync(path.resolve(codeSamplePath, `${codeSampleName}.md`))) {
        response.docs = fs.readFileSync(
          path.resolve(codeSamplePath, `${codeSampleName}.md`),
          'utf8'
        );
      }

      if (fs.existsSync(path.resolve(codeSamplePath, `${codeSampleName}.js`))) {
        response.structureddata = fs.readFileSync(
          path.resolve(codeSamplePath, `${codeSampleName}.js`),
          'utf8'
        );
      }

      return response;
    };

    // Take data input and attempt to parse as JSON
    const buildCodeSampleData = (input, filePath) => {
      try {
        return JSON.parse(input);
      } catch (ex) {
        console.log(
          error(`CodeSample data was malformed and couldn’t be parsed (${filePath})`)
        );
        return {};
      }
    };

    // Loop each codeSamples folder, attempt to grab all the things and return
    // back a fully formed object to use
    codeSamples.forEach(item => {
      const codeSampleRoot = path.resolve(basePath, item);
      const codeSampleName = getCodeSampleName(codeSampleRoot);
      const codeSampleResponse = buildCodeSample(codeSampleRoot, codeSampleName);
      const codeSampleVariantsRoot = path.resolve(codeSampleRoot, 'variants');
      const codeSampleVariantsData = codeSampleResponse.data.variants || [];

      // Error will have been logged in buildCodeSample, but this is
      // not an acceptable response.
      if (!codeSampleResponse) {
        return;
      }

      // Urls for codeSample page and preview
      codeSampleResponse.url = `${this.permalinkPrefix}/${codeSampleName}/`;
      codeSampleResponse.previewUrl = `${this.previewPrefix}/${codeSampleName}/`;
      codeSampleResponse.name = codeSampleName;

      // An empty container for variants for if one or the other methods of loading
      // them results in nothing
      codeSampleResponse.variants = [];

      // If this codeSample has a variants folder
      // run the whole process on all that can be found
      if (fs.existsSync(codeSampleVariantsRoot)) {
        const variants = getCodeSamplePaths(codeSampleVariantsRoot);

        codeSampleResponse.variants = variants.map(variant => {
          const variantRoot = path.resolve(codeSampleVariantsRoot, variant);
          const variantName = getCodeSampleName(variantRoot);

          return {
            ...{
              name: variantName,
              previewUrl: `${this.previewPrefix}/${codeSampleName}/${variantName}/`
            },
            ...buildCodeSample(variantRoot, variantName, codeSampleRoot, codeSampleName)
          };
        });
      }

      // If variants are defined in the root codeSample's config,
      // we need to render them too, using the root codeSample's markup
      if (codeSampleVariantsData.length) {
        const dataVariantItems = [];

        codeSampleVariantsData.forEach(variant => {
          dataVariantItems.push({
            ...{
              name: variant.name,
              previewUrl: `${this.previewPrefix}/${codeSampleName}/${variant.name}/`
            },
            ...buildCodeSample(codeSampleRoot, codeSampleName, null, null, {
              title: variant.title || variant.name,
              note: variant.note || '',
              configuration: variant.configuration || '',
              context: {...codeSampleResponse.data, ...variant.context} // Merge existing context with variant context so we don't have to repeat ourselves a lot
            })
          });
        });

        // Now with the data variants built, we need to loop,
        // check that a file-based one wasn't already made,
        // then add it to the collection
        dataVariantItems.forEach(variantItem => {
          const existingCodeSample = codeSampleResponse.variants.find(
            x => x.name === variantItem.name
          );

          // Variant data files take priority, so if a rendered codeSample exists, bail on this iteration
          if (existingCodeSample) {
            console.log(
              warning(
                `The variant, ${variantItem.name} was already processed with a data file, which takes priority over variants defined in the root codeSample’ (${codeSampleName}) data file`
              )
            );
            return;
          }

          codeSampleResponse.variants.push(variantItem);
        });
      }

      // Lastly, sort variants by name if codeSample hasn't
      // specifically defined source order sorting
      if (codeSampleResponse.data.sort !== 'source') {
        if (codeSampleResponse.variants) {
          codeSampleResponse.variants = codeSampleResponse.variants.sort((a, b) =>
            a.name.localeCompare(b.name)
          );
        }
      }

      result.push(codeSampleResponse);
    });

    this.processedItems = result;
    return result;
  }

  // Returns a flat array of all codeSamples and variants
  get previews() {
    const response = [];

    this.items.forEach(item => {
      // Slice only what's needed from root codeSample
      response.push({
        previewUrl: item.previewUrl,
        data: {
          title: item.data.title,
          configuration: item.data.configuration || '',
          extraSampleCSS: item.data.extraSampleCSS || ''
        },
        name: item.name,
        rendered: item.rendered
      });

      if (item.variants) {
        item.variants.forEach(variant => {
          response.push({
            ...variant,
            ...{
              name: variant.name,
              data: {
                extraSampleCSS: variant?.data?.extraSampleCSS ?? item?.data?.extraSampleCSS
              }
            }
          });
        });
      }
    });

    return response;
  }
}

module.exports = CodeSample;
