export default function() {
  const mediablocks = document.querySelectorAll( "[data-media]" ),
    createiframe = ( src, poster ) => {
      const iframe = document.createElement( "iframe" ),
        size = poster.getBoundingClientRect();

      iframe.src = src;

      iframe.style.aspectRatio = size.width > size.height ? size.width + "/" + size.height : size.height + "/" + size.width;
      iframe.setAttribute( "allow", "allowfullscreen" );

      return iframe;
    };

  mediablocks.forEach( media => {
    media.addEventListener( "click", function( e ) {
      const source = this.dataset.media,
        player = createiframe( source, this );

      this.parentNode.insertBefore( player, this );
      this.remove();

      e.preventDefault();
    });
  });
};