/* Form disclosure component. */
export default function() {
  let trigger = document.querySelectorAll( "[data-form-disclose]" ),
    init = function( el ) {
      const target = document.querySelector( `#${ el.dataset.formDisclose }` );

      if( el.validity.valid === false || el.value == false ) {
        target.setAttribute( 'aria-hidden', true );
        el.setAttribute( 'aria-expanded', false );

        el.addEventListener( 'change', ( e ) => {
          el.validity.valid && target.removeAttribute( 'aria-hidden' );
        });
        el.addEventListener( 'keydown', ( e ) => {
          el.validity.valid && target.removeAttribute( 'aria-hidden' );
        });
      }
    };

  trigger.forEach( init );
};