

### Markup Details
This component necessarily relies on source order for wayfinding. 

#### `data-toggle`
Functionality is governed by the presence of a `data-toggle` attribute. With the exception of the toggles used on [Pass-Through Flyout](http://localhost:8080/design-system/component/nav-flyout/#pass-through-flyout) components, the toggling element should always be a `button` element, as it changes the state of the current page in response to user interaction (“`a` to go somewhere, `button` to do something”).

When a value for `data-toggle` attribute is omitted, it will toggle the visibility of the element that immediately follows it in the source order.

Optionally, `data-toggle` accepts a CSS selector value to toggle the visibility of a specific element, though this should only be used to work around differences in page structure (revealing an element nested in wrapper `div`s for layout and styling, for example). For the sake of users browsing via assistive technologies, the revealed element must follow the toggle element in source order, and contain the objects that immediately follow the toggle element in the [accessibility tree](https://developer.mozilla.org/en-US/docs/Glossary/Accessibility_tree).

#### `button[role="button"]`
While not essential in all cases, adding a redundant `role="button"` to the trigger button is a sensible default. This explicit role can prevent toggles from behaving (and being narrated) as though part of a parent form element—more than just redundantly communicating “this is a button,” this role communicates “I am explicitly defining the behavior of this button” to assistive technologies.

#### `aria-expanded="[false|true]"`
`aria-expanded` attributes should be used on the trigger element, counterintuitive as that might seem, so the current state of the associated content is communicated to users before and after user interaction. If omitted, `aria-expanded="false"` will be added to any `data-toggle` element to prevent unexpected behavior.

Explicitly setting `aria-expanded="true"` will default the toggle and disclosed element states to open.


#### `aria-hidden="[false|true]"`
If omitted, `aria-hidden="true"` will be added to any element associated with a `data-toggle` element to prevent unexpected behavior. Note that failing to set this value explicitly may result in the disclosed element briefly visible on page load.

### Helper Classes
For the sake of maintaining parity between the visually renderd page and the browser’s accessibility tree, `aria-hidden` should be used as a styling hook for showing/hiding the disclosed element. Additionally, a `toggle-hidden` class will be toggled on the disclosed element.


### Focus Management & Keyboard Interaction
`Esc` should close the expanded element when open. To avoid altogether dropping user focus when `Esc` is pressed while an opened nav item has focus, user focus should be moved from inside the opened nav menu to the toggle button. 

No additional focus or keyboard management is required so long as the revealed element immediately follows the toggle in the HTML source.