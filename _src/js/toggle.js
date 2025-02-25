export default function() {
  const toggleEls = document.querySelectorAll( '[data-toggle]' ),
    closeEls = document.querySelectorAll( '[data-toggle-close]' ),
    getTarget = ( el ) => {
      // If an explicit disclosure target is set, use that; else, use the toggle's next sibling element:
      return el.dataset.toggle ? document.querySelector( el.dataset.toggle ) : el.nextElementSibling;
    },
    detectCollision = ( el ) => {
      const collisionObserver = new ResizeObserver( obs => {
        obs.forEach( ob => {
          const leftPos = ob.target.getBoundingClientRect().x;

          if( leftPos < 0 ){
            ob.target.classList.add( "left-collision" );
          }
          if( ob.target.getBoundingClientRect().x + ob.target.offsetWidth > window.innerWidth ){
            console.log( "Overflow.");
            ob.target.classList.add( "right-collision" );
          }
        });
      });
      collisionObserver.observe( el );
    },
    init = function( el ) {
      // TODO: Too early to use Element interfaces like https://caniuse.com/mdn-api_element_ariahidden ?
      const expandedSet = el.ariaExpanded,
        initialState = ( !expandedSet || expandedSet === "false" ) ? "true" : "false",
        targetEl = getTarget( el );

      // If `aria-expanded` isn't set on the toggle element, set a `false` default:
      if( expandedSet === null ) {
        el.ariaExpanded = "false";
      }

      // Set (or override) an `aria-hidden` value to match the initial state of the toggle element:
      targetEl.ariaHidden = initialState;

      // Set or remove `toggle-hidden` helper class to match the initial state of the toggle element:
      targetEl.classList[ initialState === "true" ? "add" : "remove" ]( "toggle-hidden" );

      // Bind interaction on each toggle:
      el.addEventListener( 'click', function( e ) {
          const nowCollapsed = this.ariaExpanded === "true",
            target = getTarget( this );

          // If the clicked element is a closed pass-through toggle (using `a`) that hasn't yet been toggled open, prevent navigation:
          if( !openToggles.includes( this ) && !nowCollapsed ) {
            this.localName === "a" && e.preventDefault();
          }
          swapState( this, nowCollapsed );
      });
    },
    closeHandler = ( e ) => {
      const keyPress = e.key || false;
      let targetEl;

      e.stopPropagation();

      if( openToggles.length ) {
         // SUPPORT: _Still_ too early for https://caniuse.com/mdn-javascript_builtins_array_at
        const openToggle = openToggles.filter( toggle => toggle.ariaExpanded === "true" )[ openToggles.length - 1 ],
          currentTarget = e.target.closest( "[data-toggle]" ) !== null && e.target.closest( "[data-toggle]" );
         /* SUPPORT: The above line would be better handled with either https://caniuse.com/mdn-javascript_operators_nullish_coalescing or  
            https://caniuse.com/mdn-javascript_operators_optional_chaining in the check, but both mean a steep support curve for "is it null:" */

        if( !openToggle ) {
          return;
        }

        if( e.type === "click" && 'noclickout' in openToggle.dataset ) {
          return;
        }

        if( ( ( !currentTarget.ariaExpanded ) && !getTarget( openToggle ).contains( e.target ) ) || keyPress === "Escape" ) {
          /* If the open toggle that would be closed next in the order contains an open toggle, close the inner disclosure element instead: */
          targetEl = getTarget( openToggle ).querySelector( '[data-toggle][aria-expanded="true"]' ) || openToggle;

          swapState( targetEl, true );
        }
      }
    },
    swapState = function( el, state ) {
      const target = getTarget( el ),
        // If this element is a grouped toggle, get all elements with the same group and filter out the current element: 
        others = el.dataset.toggleGroup && [ ...document.querySelectorAll( `[data-toggle-group="${ el.dataset.toggleGroup }"`) ].filter( grouped => grouped !== el ),
        toggleState = ( el, target, state ) => {
          // Swap the states of the toggle element and the target element on user interaction:
          el.setAttribute( 'aria-expanded', !state );
          target.classList.toggle( "toggle-hidden", state );

          // If the element has an `aria-hidden` attribute, toggle the state of that as well:
          if( target.ariaHidden ) {
            target.ariaHidden = state;
          }
        };

      if( !state ) {
        // If we're opening the disclosure element, add this toggle to the history stack:
        openToggles.push( el );
      } else {
        // If we're closing the disclosure element, remove this toggle fron the history stack:
        openToggles.splice( openToggles.indexOf( el ), 1 );
      }

      // If the clicked element is a grouped toggle, close all associated toggles:
      if( state === false && others ) {
        others.forEach( other => other.ariaExpanded === "true" && swapState( other, true ) );
      }

      // Return focus to the toggle only if user focus is currently inside the revealed element:
      if( target.contains( document.activeElement ) ) {
        el.focus();
      }
      
      toggleState( el, target, state );

      if( !state ) {
      // If necessary prevent the disclosure element from colliding with the browser viewport:
        detectCollision( target );
      }
    };

  // Initialize the "history" array used by the `esc`/modal handler with any already-open toggles: 
  let openToggles = [ ...document.querySelectorAll( "[data-toggle][aria-expanded='true']" ) ];

  // Initialize toggle elements: 
  toggleEls.forEach( el => { 
    init( el );
  });

  closeEls.forEach( el => {
    el.addEventListener( 'click', function( e ) {
      const targets = document.querySelectorAll( `[data-toggle="${ this.dataset.toggleClose }"]` );

      [...targets ].filter( toggle => toggle.ariaExpanded === "true" ).forEach( open => open.click() );
    });
  });

  // Handle click outside a toggle or active disclosed content:
  document.addEventListener( 'click', closeHandler );

  // Close open toggles on `esc` in the order they were opened:
  document.addEventListener( 'keyup', closeHandler );
};