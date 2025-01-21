import $ from 'jquery'

export function adminAcf() {
  function niceAccordion(field) {
    const title = field.$el.closest('[data-block]').data('title')

    const block = acf.data.blockTypes.find(block => block.title === title);
    if (!block) return;

    const {
      template_directory_uri,
      render_template
    } = acf.data.blockTypes.filter(block => block.title === title)[0];
    const imageUrl = render_template.replace('index.php', 'screenshot.png');
    field.$el.find('label').first().text(title)
    field.$el.find('.acf-accordion-title').first()
      .css('background-image', `url(${template_directory_uri}/${imageUrl})`)

    // close opened accordions in wp block columns
    if (field.$el.hasClass('-open') && field.$el.closest('.wp-block-columns').length > 0) {
      field.$el.find('.acf-accordion-title').first().unbind().click();
    }

    // region wp-columns-block popup
    field.$el.find('.acf-accordion-title').first().unbind().on('click', function () {
      let self = $(this);
      if (self.closest('.wp-block-columns').length > 0) {
        let parentTitle = self.closest('.acf-block-fields');
        parentTitle.toggleClass('acf-block-fields-popup-activated');
        document.body.classList.toggle('acf-popup-activated');
        parentTitle.append('<span class="close-acf-popup">x</span>');
        if (!document.body.classList.contains('acf-popup-activated')) {
          parentTitle.find('.close-acf-popup')?.remove();
        }
      }
    })
    $(document).on('click', '.close-acf-popup', function () {
      $(this).closest('.acf-block-fields-popup-activated').find('.acf-accordion-title')?.click();
    });
    // endregion wp-columns-block popup
  }

  window.acf?.addAction('load_field/name=block_preview_image', niceAccordion)
  window.acf?.addAction('append_field/name=block_preview_image', niceAccordion)
}

