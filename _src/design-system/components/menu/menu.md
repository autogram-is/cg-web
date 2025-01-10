A dropdown _menu_ must only contain elements intended to trigger actions on the page/site, such as user preferences. For all other use cases, see [Visibility Toggle](/component/toggle/).

## Markup Details

`role="menu"`
Specifically flags a dropdown as being a set of controls, not navigation items. Note that the button that shows/hides this element should be located outside of this element.

`role="menuitem"`
Specifically flags individual items within the menu as the controls associated with the menu, which may be sprinkled in with non menu items (but in practice, shouldn’t be).

`aria-haspopup="true"`
The WAI-ARIA 1.0 spec defined aria-haspopup as containing a boolean value exclusive to menus. It has since been redefined to apply to all flyouts (apart from tooltips), but this is a relatively new change, and common browser/assistive tech implementations haven’t been updated to match. As such, I recommend only using this on menu dropdowns.


### Focus Management
Upon revealing the associated menu, user focus must be programmatically shifted to the first focusable element in the newly opened menu.

### Keyboard Interaction
https://www.w3.org/TR/wai-aria-practices-1.1/#menubutton

Note that custom keyboard interactions will be overridden in some assistive browsing contexts.

**Down/Right Arrows (in menu):**
Move to next menu item (optionally: if focus is on last menu item, cycle to first)

**Up/Left Arrows (in menu):**
Move to previous menu item (optionally:  if focus is on first menu item, cycle to last)

**Esc (anywhere in document):**
Close open menu(s)
