export default function() {
  const mapSelector = ".interactive-map",
    maps = document.querySelectorAll( mapSelector ),
    initializeCard = ( card ) => {
      const included = card.dataset.includes && card.dataset.includes.split(","),
        highlightColor = card.dataset.highlightColor;

      included.forEach( state => {
        const statePath = map.querySelector( `#${ state }` );

        if( statePath ) {
          statePath.style.fill = highlightColor;
        }
      });

      card.addEventListener( 'mouseover', highlightRegion );
      card.addEventListener( 'mouseout', inactiveRegion );

      card.addEventListener( 'focusin', highlightRegion );
      card.addEventListener( 'focusout', inactiveRegion );
    },
    highlightRegion = function( card ) {
      const region = this.id,
        highlightColor = this.dataset.highlightColor,
        included = this.dataset.includes && this.dataset.includes.split(",");

      included && included.forEach( state => {
        const statePath = map.querySelector( `#${ state }` );

        if( !statePath ) {
          return;
        }
        statePath.classList.add( "active-region" );
      });
    },
    inactiveRegion = function() {
      this.closest( mapSelector ).querySelectorAll( ".active-region" ).forEach( state => {
        state.classList.remove( "active-region" );
      });
    },
    highlightCard = function() {
      const state = this.id,
        card = document.querySelector( `[data-includes*="${ state }"]` );

      let included, highlightColor;

      if( !card ) {
        return;
      }

      included = card.dataset.includes && card.dataset.includes.split(",");
      highlightColor = card.dataset.highlightColor;

      included && included.forEach( stateId => {
        const state = map.querySelector( `#${ stateId }` );

        if( !state ) {
          return;
        }

        state.classList.add( "active-region" );
        state.style.fill = highlightColor;
      });

      card && card.classList.add( "active-card" );
    },
    mapNavigation = function() {
      const state = this.id,
        card = this.closest( mapSelector ).querySelector( `[data-includes*="${ state }"]` );

      window.location.href = card.href;
    };

  maps.forEach( ( map ) => {
    const cards = map.querySelectorAll( ".card-location" ),
      paths = map.querySelectorAll( "svg path" );

    cards.forEach( initializeCard );

    paths.forEach( path => {
      path.addEventListener( 'click', mapNavigation );
      path.addEventListener( 'mouseover', highlightCard );
      path.addEventListener( 'mouseout', function( e ) {
        const state = this.id,
          cards = map.querySelectorAll( `[data-includes*="${ state }"]` );

        cards.forEach( card => {
          const included = card.dataset.includes && card.dataset.includes.split(","),
            highlightColor = card.dataset.highlightColor;

          included.forEach( stateId => {
            const state = map.querySelector( `#${ stateId }` );

            if( !state ) {
              return;
            }
            state.classList.remove( "active-region" );
          });
  
          card.classList.remove( "active-card" );
        }, { once : true });
      });
    });
  });
};