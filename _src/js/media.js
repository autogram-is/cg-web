export default function() {
  const mediablocks = document.querySelectorAll( "[data-media]" ),
    createiframe = ( src, poster ) => {
      const iframe = document.createElement( "iframe" );

      iframe.src = src + "?autoplay=1";
      iframe.setAttribute( "allow", "allowfullscreen" );
      iframe.setAttribute( "allow", "autoplay" );

      return iframe;
    };

  mediablocks.forEach( media => {
    media.addEventListener( "click", function( e ) {
      const source = this.dataset.media,
        player = createiframe( source, this ),
        poster = this.querySelector('img').src;

      this.parentNode.style.backgroundImage = "url(" + poster + ")";
      this.parentNode.insertBefore( player, this );
      this.remove();

      e.preventDefault();
    });
  });
};