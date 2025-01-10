export default function() {
  const toggleEls = document.querySelectorAll( '[aria-haspopup]' ),
    init = ( el ) => { 
      el.addEventListener( 'click', toggleStates );
      document.addEventListener( 'keyup', keyHandler );
    },
    closeTop = ( e ) => {
      const openToggle = document.querySelector( '[aria-haspopup][aria-expanded="true"]' );
      let targetEl;

      if( openToggle ) {
        targetEl = openToggle.closest( '.menu' ).querySelector( '[role="menu"]' );

        // If another menu is open, close it:
        targetEl.setAttribute( 'aria-hidden', true );
        openToggle.setAttribute( 'aria-expanded', false );
      }
    },
    keyHandler = ( e ) => {
      const current = document.activeElement,
          openWrap = document.querySelectorAll( '[aria-hidden="false"][role="menu"]' ),
          parentWrap = current.closest( '.menu' ),
          resultList = parentWrap && parentWrap.querySelector( '[role="menu"]' ),
          matchedSibling = ( el, sel, dir ) => {
            let sibEl = el[ dir + "ElementSibling" ];

            while( sibEl ) {
              if( sibEl.matches( sel ) ) {
                return sibEl;
              }
              sibEl = sibEl[ dir + "ElementSibling" ];
            }
          };
      let moveTo;

      if( !current || !openWrap ) {
        return;
      }
           
      if( e.key === "Escape" ){
        closeTop( e, true );

        // If focus is currently inside a menu, return focus to the trigger:
        if( parentWrap ) {
          parentWrap.querySelector( '[aria-haspopup="true"]' ).focus();   
        }
        return;
      }
       
      switch( e.key ) {
        case "ArrowDown":
        case "ArrowRight":
          // Move focus to the next menu item, or jump to the first at the end of the list.
          moveTo = matchedSibling( current, '[role="menuitem"]', 'next' ) || resultList.firstElementChild;
          break;
        case "ArrowUp":
        case "ArrowLeft":
          // Move focus to the previous menu item, or jump to the last at the start of the list.
          moveTo = matchedSibling( current, '[role="menuitem"]', 'previous' ) || resultList.lastElementChild;
          break;
      }
      if( moveTo !== undefined ) {
        moveTo.focus();
        e.preventDefault();
      }
    },
    toggleStates = function() {
      let target = this.parentNode.querySelector( '[role="menu"]' ),
        nowCollapsed = this.getAttribute( 'aria-expanded' ) === "true";

      /* Swap the states of the toggle element and the target element on user interaction: */
      target.setAttribute( 'aria-hidden', nowCollapsed );
      this.setAttribute( 'aria-expanded', !nowCollapsed );
      
      if( !nowCollapsed ) {
        // Add realignment class if opened menu would collide with the right side of the viewport:
        target.classList[ target.getBoundingClientRect().right > window.innerWidth ? 'add' : 'remove' ]( 'realign' );

        // Auto-focus the first menuitem when menu is expanded:
        target.firstElementChild.focus();
      }
    };

  toggleEls.forEach( el => { 
    init( el );
  });

};