const fs = require('fs');
const path = require('path');
const chalk = require('chalk');

const baseIndex = process.argv.indexOf('-b');
const pathIndex = process.argv.indexOf('-p');
const nameIndex = process.argv.indexOf('-n');
const titleIndex = process.argv.indexOf('-t');
const addJs = process.argv.indexOf('-i') >= 0;
const skipMarkupIndex = process.argv.indexOf('-sm');
const basePath = [process.cwd(), '_src/design-system'];
const cssPath = [process.cwd(), '_src/css/components'];
const jsPath = [process.cwd(), '_src/js'];

// Set up the chalk warning and error states
const warning = chalk.black.bgYellow;
const error = chalk.black.bgRed;
const success = chalk.black.bgGreen;

if (pathIndex > 0 && nameIndex > 0) {
  const passedBasePath = process.argv.slice(baseIndex + 1)[0];
  const newPath = process.argv.slice(pathIndex + 1)[0];
  const name = process.argv.slice(nameIndex + 1)[0];
  const title = process.argv.slice(titleIndex + 1)[0];
  const camelName = name.replace(/-([a-z])/gi, (s, g) => g.toUpperCase() );
  const isVariant = newPath.includes('variants');
  let keyLinks = '';

  // This one is only used for variants
  const skipMarkup = process.argv.slice(skipMarkupIndex + 1)[0] === 'true' ? true : false;

  basePath.push(passedBasePath);
  basePath.push(newPath);

  if (name !== newPath) {
    basePath.push(name);
  }

  // Create the directory if it doesn't already exist
  if (!fs.existsSync(path.join(...basePath))) {
    fs.mkdirSync(path.join(...basePath), {recursive: true});
  }

  // Create markup if not skipping (variants only)
  if (isVariant && skipMarkup) {
    console.log(warning('Markup skipped for this variant'));
  } else {
    fs.writeFileSync(path.join(...[...basePath, `${name}.twig`]), `<!-- ${ title || name } -->`);
  }

  if (!isVariant) {
    // Create documentation Markdown file
    fs.writeFileSync(path.join(...[...basePath, `${name}.md`]), '');

    // Generate CSS/keylinks entry for all components.
    if( passedBasePath === "components" ) {
      keyLinks += `{
        "label": "CSS",
        "url": "/_src/css/components/${name}.css"
      }`

      // If it doesn’t already exist, create the associated CSS file
      if (!fs.existsSync(path.join(...[...cssPath, `${name}.css`]))) {
        fs.writeFileSync(path.join(...[...cssPath, `${name}.css`]), `.${ name } {}`);
      }

      // If the interactive flag (`-i`) is set
      if ( addJs ) {
        // If it doesn’t already exist, create the associated JS file and add it to the bundle
        if( !fs.existsSync(path.join(...[...jsPath, `${name}.js`]))) {
          fs.writeFileSync(path.join(...[...jsPath, `${name}.js`]), `/* ${title || name} */`);
          fs.appendFileSync(path.join(...[...jsPath, `bundle.js`]), 
            `
  import ${camelName} from './${name}.js';
  ${camelName}();`
          );
        }
        // Append a keyLink entry for the JavaScript file in the component JSON
        keyLinks += `, {
        "label": "JS",
        "url": "/_src/js/${name}.js"
      }`
      }
    }

    if( passedBasePath === "wireframes" ) {
      fs.writeFileSync(path.join(...[...basePath, `${name}.js`]), '');
    }
  }


  fs.writeFileSync(
    path.join(...[...basePath, `${name}.json`]),
      `
{ 
  "title": "${title || name}",
  "name" : "${ name }",
  "keyLinks": [ ${ keyLinks } ],
  "context": {
  }
}
`
  );

  console.log(success(`${isVariant ? 'Variant' : 'Pattern'} created!`));
} else {
  console.log(error('Name (-n) and/or Path (-p) not defined'));
}
