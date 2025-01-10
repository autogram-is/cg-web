## Markup Patterns and Interactions

### `article[aria-labelledby]`

As each individual listing can stand alone—eg. each indexed item be syndicated to an external site and still allow users to preview and visit their associated pages—`article` is the most suitable wrapper element, counterintuitive as that might seem.


## Structured Data

The net benefit of embedding a redundant representation of a resource list is questionable, especially considering there are other mechanisms (like [Atom](https://tools.ietf.org/html/rfc4287) feeds) that perform the same function.

```
<link rel="alternate" href="resource-list-page.atom"/>
```

The main concern is that moving away from the expand-in-place pattern means implementing pagination, and it is common to denote the pagination somehow in the list's URL. Since URLs are _identifiers_ in addition to _locators_, without special consideration, structured data for each page in a paginated resource list would be asserting is that each segment of the list is a distinct list containing only the elements on that page.

With such a resource, one could link to a JSON-LD variant instead of embedding it. Should this avenue be explored in the future: mechanisms do exist for meaningfully representing resource lists, though they’re of limited practical use.