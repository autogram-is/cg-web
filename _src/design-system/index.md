---
title: 'Welcome to the Cumming Group Design System'
layout: 'layouts/docs-page.twig'
permalink: '/index.html'
---

<div class="wrapper py-m">
  <div class="design-system__inner flow">


It’s recommended that you use this system as **the single source of truth** for the UI.


## CSS

The CSS is processed with [PostCSS](https://postcss.org/) and uses [CUBE CSS](https://cube.fyi/) as the methodology. [Tailwind CSS](https://github.com/tailwindlabs/tailwindcss) is used (_sparingly_) as a utility class and CSS Custom Property generator. All partials are bundled into one single output CSS file: `global.css`.

## CSS folder structure

```
_src
└── css
    ├── components
    ├── compositions
    ├── global
    ├── utilities
    └── global.css
```

The CSS folder mostly resembles the CUBE CSS structure and is as follows:

1. `components`: [self-contained “building block” components](/design-system/all-components/)
2. `compositions`: [layout compositions](/design-system/css-compositions/)
3. `utilities`: [core utilities](/design-system/css-utilities/)
4. `global`: partials containing global styles
5. `global.css`: the main CSS file that pulls everything together

## JavaScript

The only JavaScript output from this design system is _extremely light_ user-interface code. Native interaction patterns should be prioritized whenever possible (eg. `details`/`summary` or `dialog`), with custom scripting used only to polyfill, provide reasonable fallback behaviors, or shim behaviors missing from native implementations (eg. assigning and toggling ARIA attributes).

## Use the component generator

To prevent repetitive file creation, you can use the pattern generator to create a new pattern or pattern variant from the command line:

### Creating new components

Let's say you want a new pattern called "my-pattern":

```bash
npm run components:create -- -p my-pattern -n my-pattern -t My\ Pattern
```

This command will create the following folder and file structure:

```
_src/
  └── components/
    └── my-pattern/
      ├── my-pattern.twig
      ├── my-pattern.json
      └── my-pattern.md
  └── css/
    └── components/
      └── my-pattern.css
```

No further action is required to add this component to the library, and the newly-created CSS file will be included in the global.css file automatically.

If the pattern is an interactive component that requires custom JavaScript, include the `-i` flag:

```bash
npm run components:create -- -i -p my-pattern -n my-pattern -t My\ Pattern
```

This will result in the following folder and file structure:

```
_src/
  └── components/
    └── my-pattern/
      ├── my-pattern.twig
      ├── my-pattern.json
      └── my-pattern.md
  └── css/
    └── components/
      └── my-pattern.css
  └── js/
    └── my-pattern.js
```

This command will also update `_src/js/bundle.js` to include:

```javascript
import myPattern from './my-pattern.js';
myPattern();
```

#### Variants

That there are two methods of creating [variants](https://cube.fyi/exception.html) of an existing pattern. 

First, a variant can be created from the command line. For example, to create a "secondary" variant of the "my-pattern" component created above, use the following command:

```bash
npm run components:create -- -p my-pattern/variants -n secondary-pattern -t Secondary
```

This will result in the addition of the following folder and file structure to the one already established by the base component:

```
src/
  └── design-system/
      └── components/
        └── my-pattern/
          ├── ...
          └── variants/
            ├── secondary-pattern.twig
            └── secondary-pattern.json
```

Note that no new CSS file is created, as the variant is expected to share and inherit styles from the base component.

Simple variants can also be created by adding a `variants` property to the base component's JSON file containing an array of variant definitions. For example, an "inverted" variant of a button might be added to `button.json`:

```javascript
{
  "title": "Button",
  "context": {
    "text": "Action"
  },
  "variants": [
    {
      "title": "Inverted",
      "context": {
        "type": "inverted",
        "text": "Action"
      }
    }
  ]
}
```

#### Arguments

There are 2 required arguments to pass in—`-p` and `-n`. The rest are optional.

Make sure you add the `--` _after_ `npm run patterns:create` so the arguments get passed into the task.

Make sure you escape spaces with a `\`.

- `-p` is the path from `_src/design-system/components`
- `-n` is the file name
- `-t` is the title. If omitted, the filename will be used
- `-i` an interactive component, requiring a dedicated JavaScript file
- `-sm` allows you to skip markup being generated if you are generating a variant


### Creating top-level wireframes

Let's say you want a new page wireframe called "my-wireframe":

```bash
npm run wireframe:create -- -p my-wireframe -n my-wireframe -t My\ Wireframe
```

This command will create the following folder and file structure:

```
_src/
  └── wireframes/
    └── my-wireframe/
      ├── my-pattern.twig
      ├── my-pattern.json
      └── my-pattern.md
```

No further action is required to add this wireframe to the library.

#### Wireframe sub-pages

Wireframe sub-pages are handled in much the same way component variants are. As with component variants, there are two methods of creating sub-pages of an existing wireframe. 

First, a sub-page can be created from the command line. For example, to create a "detail page" variant of the "my-wireframe" wireframe created above, use the following command:

```bash
npm run components:create -- -p my-wireframe/variants -n detail-page -t Detail\ Page
```

This will result in the addition of the following folder and file structure to the one already established by the base component:

```
src/
  └── design-system/
      └── components/
        └── my-pattern/
          ├── ...
          └── variants/
            ├── secondary-pattern.twig
            └── secondary-pattern.json
```

Note that no new CSS file is created, as the variant is expected to share and inherit styles from the base component.

</div>
</div>
