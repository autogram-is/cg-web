/* Disclosure component. */
export default function() {
  let details = document.querySelectorAll( "details" ),
    toggle = ( state, { summary, details } ) => {
      details.forEach( el => {
        el.setAttribute( 'aria-hidden', !state );
      });
      summary.setAttribute( 'aria-expanded', state );
    };

  details.forEach( el => {
    const current = !!el.getAttribute( "open" ),
      summary = el.querySelector( "summary" ),
      details = el.querySelectorAll( ":scope > :not(summary)" );

    toggle( current, { summary, details } );

    el.addEventListener( "toggle", e => {
      toggle( e.target.open, { summary, details } );
    });

    /* Disclosed elements should be hidden when `esc` is pressed. */
    el.addEventListener( "keyup", e => {
      if( e.key.toLowerCase() === "escape" ) {
        e.target.closest( "[open]" ).removeAttribute( "open" );
        toggle( false, { summary, details } );
      }
    });
  });
};