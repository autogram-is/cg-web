An `aria-label` attribute has been added to the `nav` element, as `nav` is used elsewhere on the page (eg. the primary navigation)—where there are multiple `nav` elements on a single page, all must be given a unique accessible name via `aria-label` or `aria-labelledby`.

For all pagination links excluding the current page, `<span class="visuallyhidden">page</span>` is added to provide additional context to the link wording for users of Assistive Technology.

The current page is indicated by `aria-current="page"`.