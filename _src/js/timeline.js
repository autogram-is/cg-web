export default function() {
  const timelines = document.querySelectorAll( '.timeline' ),
    itemObserver = new IntersectionObserver( obs => {
      obs.forEach( ob => {
        if( ob.isIntersecting ) {
          ob.target.querySelector( ".timeline-inner" ).classList.add( 'show-inner' );
          itemObserver.unobserve( ob.target );
        }
      });
    }, {
      threshold: 0.5
    }),
    timelineObserver = new IntersectionObserver( obs => {
      obs.forEach( ob => {
        if( ob.isIntersecting ) {
          drawLine( ob.target );
        }
      });
    }),
    drawLine = ( timeline ) => {
        const timelineCards = timeline.querySelectorAll( '.timeline-item' ),
          timelineHeadings = timeline.querySelectorAll( '.hed' );

        timeline.addEventListener( "transitionend", ( e ) => {
          setTimeout( () => {
            timelineCards.forEach( ( card, i )=> setTimeout( () => card.classList.add( "show-card" ), i * 200 ) );
          }, 200);
        });

        timelineHeadings.forEach( ( hed, i )=> setTimeout( () => hed.classList.add( "show-hed" ), i * 200 ) );

        timeline.classList.add( 'show-line' );
        timeline.classList.remove( 'hide-line' );

        timelineCards.forEach( card => itemObserver.observe( card ) );
    };

  timelines.forEach( timeline => timelineObserver.observe( timeline ) );
};