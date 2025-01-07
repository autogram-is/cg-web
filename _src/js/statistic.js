export default function() {
  const opt = {
      "duration" : 2000,
      "frameDuration" :  2000 / 60,
      "easing" : t => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2 //EaseInOutQuad
    },
    counters = document.querySelectorAll( "[data-counter]" ),
    statObserver = new IntersectionObserver( obs => {

      obs.forEach( ob => {
        if( ob.isIntersecting ) {
          ob.target.classList.add( "statistic-visible" );

          const headings = ob.target.querySelectorAll( ".type-hed" );

          headings.forEach( el => {
            const textNode = [ ...el.childNodes ].filter( ( node ) => node.nodeType === Node.TEXT_NODE && node.nodeValue.trim() !== "" && !isNaN( parseFloat( node.nodeValue ) ) ),
              value = textNode.map( ( el ) => el.textContent ).join(""),
              valueFloat = parseFloat( value.replace(/,/g, ''), 10 ),
              totalFrames = Math.round( opt.duration / opt.frameDuration );

            [...textNode ].forEach( node => node.nodeValue = 0 );
            let frame = 0;

            const ticker = setInterval( () => {
              frame++;
              const currentCount = Math.round( valueFloat * opt.easing( frame / totalFrames ) );

              [...textNode ].forEach( node => {
                node.nodeValue = formatNumber( currentCount );
              });

              if ( valueFloat === currentCount || frame === totalFrames ) {
                clearInterval( ticker );
              }
            }, opt.frameDuration );
          });
          statObserver.unobserve( ob.target );
        }
      });
    }, {
      threshold: 0.5
    }),
    formatNumber = function( num ) {
      const regEx = /(\d+)(\d{3})/;
      let numStr = num.toString();

      while( regEx.test( numStr ) ) {
        numStr = numStr.replace(regEx, '$1' + "," + '$2');
      }

      return numStr;
    };


  counters.forEach( statblock => {
    statObserver.observe( statblock );
  });
};