(function ($) {
  $(function () {
    // Show/hide option when type changes
    $('.js-swv-type')
      .on('change', function () {
        var type = $(this).val();

        if ('color' === type) {
          $('.js-swv-color-field').removeClass('hidden');
          $('.js-swv-image-field').addClass('hidden');
        } else if ('image' === type) {
          $('.js-swv-color-field').addClass('hidden');
          $('.js-swv-image-field').removeClass('hidden');
        } else {
          $('.js-swv-color-field, .js-swv-image-field').addClass('hidden');
        }
      })
      .trigger('change');

    // Init color picker
    $('.js-swv-color').wpColorPicker();

    // Init image handler
    $('.js-swv-remove-image').on('click', function (evt) {
      evt.preventDefault();

      $('.js-swv-image')
        .val('')
        .closest('.js-swv-image-field')
        .find('.js-swv-image-placeholder')
        .attr('src', swv_params.placeholder_url);
    });

    var file_frame;
    $('.js-swv-update-image').on('click', function(evt) {
      evt.preventDefault();

      if ( undefined !== file_frame ) {

        file_frame.open();
        return;

      }

      file_frame = wp.media.frames.file_frame = wp.media({
        title: swv_params.choose_image,
        button: {
          text: swv_params.choose_image
        },
        multiple: false
      });

      file_frame.on( 'select', function() {

        var json = file_frame.state().get( 'selection' ).first().toJSON();

        // First, make sure that we have the URL of an image to display
        if ( 0 > $.trim( json.url.length ) ) {
          return;
        }

        $('.js-swv-image')
          .val(json.id)
          .closest('.js-swv-image-field')
          .find('.js-swv-image-placeholder')
          .attr('src', json.url);
      });

      // Now display the actual file_frame
      file_frame.open();
    });
  });
})(jQuery);