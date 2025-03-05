export default function() {
  const toggleEls = document.querySelectorAll( '[data-toggle]' ),
    closeEls = document.querySelectorAll( '[data-toggle-close]' ),
    getTargets = ( el ) => {
      if( !el ) {
        return;
      }
      // If explicit disclosure targets are set, use those; else, use the toggle's next sibling element:
      return el.dataset.toggle ? document.querySelectorAll( el.dataset.toggle ) : [ el.nextElementSibling ];
    },
    detectCollision = ( el ) => {
      const collisionObserver = new ResizeObserver( obs => {
        obs.forEach( ob => {
          const leftPos = ob.target.getBoundingClientRect().x;

          if( leftPos < 0 ){
            ob.target.classList.add( "left-collision" );
          }
          if( ob.target.getBoundingClientRect().x + ob.target.offsetWidth > window.innerWidth ){
            ob.target.classList.add( "right-collision" );
          }
        });
      });
      collisionObserver.observe( el );
    },
    init = function( el ) {
      // TODO: Too early to use Element interfaces like https://caniuse.com/mdn-api_element_ariahidden ?
      const expandedSet = el.getAttribute( "aria-expanded" ),
        sessionAttr     = el.getAttribute( "data-session" ),
        initialState    = ( !expandedSet || expandedSet === "false" ) ? "false" : "true",
        targetEls       = getTargets( el );
      let state         = sessionAttr !== null && localStorage.getItem( sessionAttr ) !== null ? localStorage.getItem( sessionAttr ).toString() : initialState.toString();

    // If `aria-expanded` isn't set on the toggle element and there's no remembered state, set a `false` default:
      if( expandedSet === null && sessionAttr === null ) {
        state = "false";
      }

      el.setAttribute("aria-expanded", state );

      if( sessionAttr !== null && localStorage.getItem( sessionAttr ) === null ) {
        // If there's a `data-session` attribute but no localStorage entry yet, create one based on the initial `aria-expanded` state.
        localStorage.setItem( sessionAttr, expandedSet );
      }

      targetEls.forEach( targetEl => {
        // Set (or override) an `aria-hidden` value to match the initial state of the toggle element:
        targetEl.setAttribute( "aria-hidden", ( state === "true" ? "false" : "true" ) );

        // Set or remove `toggle-hidden` helper class to match the initial state of the toggle element:
        targetEl.classList[ state === "true" ? "remove" : "add" ]( "toggle-hidden" );
      });

      // Bind interaction on each toggle:
      el.addEventListener( 'click', function( e ) {
          const nowCollapsed = this.getAttribute( "aria-expanded" ) === "true";

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
        const selectedToggles = openToggles.filter( toggle => toggle.getAttribute( "aria-expanded" ) === "true" && toggle.getAttribute( "data-persist" ) === null ),
          openToggle = selectedToggles[ selectedToggles.length - 1 ],
          currentTarget = e.target.closest( "[data-toggle]" ) !== null && e.target.closest( "[data-toggle]" ),
          openTargets = getTargets( openToggle );

        let targetEls;
         /* SUPPORT: The above line would be better handled with either https://caniuse.com/mdn-javascript_operators_nullish_coalescing or  
            https://caniuse.com/mdn-javascript_operators_optional_chaining in the check, but both mean a steep support curve for "is it null:" */

        if( !openToggle ) {
          return;
        }

        openTargets.forEach( openTarget => {
          if( e.target.closest( "[data-toggle-close]" ) !== null || !openTarget.contains( e.target ) && !openToggle.contains( e.target ) || keyPress === "Escape"  ) {
            /* If the open toggle that would be closed next in the order contains an open toggle, close the inner disclosure element instead: */
            targetEls = getTargets( openToggle ).forEach( targetEl => {
              swapState( openToggle, "true" );
            });
          }
        });

      }
    },
    swapState = function( el, state, related = false ) {
      const targets = getTargets( el ),
        // If this element is a grouped toggle, get all elements with the same group and filter out the current element: 
        others = el.getAttribute( "data-toggle-group" ) && [ ...document.querySelectorAll( `[data-toggle-group="${ el.getAttribute("data-toggle-group") }"`) ].filter( grouped => grouped !== el ),
        toggleState = ( el, target, state ) => {
          // Swap the states of the toggle element and the target element on user interaction:
          el.setAttribute( 'aria-expanded', !state );
          target.classList.toggle( "toggle-hidden", state );

          if( el.getAttribute( "data-session" ) !== null ) {
            localStorage.setItem( el.getAttribute( "data-session"), !state );
          }

          // If the element has an `aria-hidden` attribute, toggle the state of that as well:
          targets.forEach( target => {
            if( target.getAttribute( "aria-hidden" ) ) {
              target.setAttribute( "aria-hidden", state );
            }
          });
        };

      // If the clicked element is a grouped toggle, close all associated toggles:
      if( state === false && others ) {
        others.forEach( other => other.getAttribute( "aria-expanded" ) === "true" && swapState( other, true, true ) );
      }

      if( state && el.getAttribute( 'data-persist' ) !== null && !related ) {
        return;
      }

      if( !state ) {
        // If we're opening the disclosure element, add this toggle to the history stack:
        openToggles.push( el );
      } else {
        // If we're closing the disclosure element, remove this toggle fron the history stack:
        openToggles.splice( openToggles.indexOf( el ), 1 );
      }

      // If the clicked element is a grouped toggle, close all associated toggles:
      if( state === false && others ) {
        others.forEach( other => other.getAttribute( "aria-expanded" ) === "true" && swapState( other, true ) );
      }

      targets.forEach( target => {
        if( target === null ) {
          return;
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
      });
    };

  // Initialize the "history" array used by the `esc`/modal handler with any already-open toggles: 
  let openToggles = [ ...document.querySelectorAll( "[data-toggle][aria-expanded='true']" ) ];

  // Initialize toggle elements: 
  toggleEls.forEach( el => { 
    init( el );
  });

  closeEls.forEach( el => {
    el.addEventListener( 'click', function( e ) {
      const targets = document.querySelectorAll( `[data-toggle="${ this.getAttribute('[data-toggle-close]') }"]` );

      [...targets ].filter( toggle => toggle.getAttribute( "aria-expanded" ) === "true" ).forEach( open => open.click() );
    });
  });

  // Handle click outside a toggle or active disclosed content:
  document.addEventListener( 'click', closeHandler );

  // Close open toggles on `esc` in the order they were opened:
  document.addEventListener( 'keyup', closeHandler );
};