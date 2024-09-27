# Cumming Group Wordpress Theme

## The `CGSite` class

In **functions.php**, we call `new CGSite();`. The `CGSite` class sits in the **src** folder. You can update this class to add functionality to your theme. This approach is just one example for how you could do it.

The **src** folder would be the right place to put your classes that [extend Timber’s functionality](https://timber.github.io/docs/v2/guides/extending-timber/).

Small tip: You can make use of Composer’s [autoloading functionality](https://getcomposer.org/doc/04-schema.md#psr-4) to automatically load your PHP classes when they are requested instead of requiring one by one in **functions.php**.

## What else is there?

- `static/` is where you can keep your static front-end scripts, styles, or images. In other words, your Sass files, JS files, fonts, and SVGs would live here.
- `views/` contains all of your Twig templates. These pretty much correspond 1 to 1 with the PHP files that respond to the WordPress template hierarchy. At the end of each PHP template, you’ll notice a `Timber::render()` function whose first parameter is the Twig file where that data (or `$context`) will be used. Just an FYI.
- `tests/` ... basically don’t worry about (or remove) this unless you know what it is and want to.

## Other Resources

* [This branch](https://github.com/laras126/timber-starter-theme/tree/tackle-box) of the Timber starter theme has some more example code with ACF and a slightly different set up.
* [Twig for Timber Cheatsheet](http://notlaura.com/the-twig-for-timber-cheatsheet/)
* [Timber and Twig Reignited My Love for WordPress](https://css-tricks.com/timber-and-twig-reignited-my-love-for-wordpress/) on CSS-Tricks
