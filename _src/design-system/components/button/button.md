## Usage

The `.button` class should only be applied to `<button>` and `<a>` elements. `input[type="submit"` should be avoided in favor of `button` whenever possible, as the latter is functionally identical, but allows for much more robust styling.

> The attribute's missing value default and invalid value default are both the Submit Button state.
>
> — [HTML Living Standard](https://html.spec.whatwg.org/multipage/form-elements.html#the-button-element)

`<button>` should only be used when user interaction would trigger a change on the current page—for example, toggling the visiblity of another element. `<a>` should be used whenever user interaction will result in navigation—for example, moving to another page or scrolling the viewport to another element on the current page. As a general rule: “`a` to go somewhere, `button` to do something.”

### Markup Details

#### `button[role="button"]`
While not necessary in all cases, a redundant `role=button` attribute on `button` elements signals to assistive technologies that the button is unrelated to any form that contains it—explicitly flagging the `button` as having an effect on the current page, regardless of the context in which it appears.