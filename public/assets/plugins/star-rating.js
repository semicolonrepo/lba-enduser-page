// configure the class for runtime loading
if (!window.fbControls) window.fbControls = new Array();

window.fbControls.push(function (controlClass) {
  /**
   * Star rating class
   */
  class controlStarRating extends controlClass {
    configure() {
      this.js = '/assets/plugins/jquery.rateyo.min.js';
      this.css = '/assets/plugins/jquery.rateyo.min.css';
    }

    /**
     * build a text DOM element, supporting other jquery text form-control's
     * @return DOM Element to be injected into the form.
     */
    build() {
      return this.markup('span', null, {id: this.config.name});
    }

    onRender() {
      $('#form-render').append(`<input type='hidden' name='${this.config.name}' />`);

      let value = this.config.value || 0;
      $(`#${this.config.name}`).rateYo({
        rating: value,
        spacing: '5px',
        fullStar: true,
        onSet: function (rating, rateYoInstance) {
          $(`#form-render input[name='${rateYoInstance.node.id}']`).val(rating);
        }
      });
    }
  }

  // register this control for the following types & text subtypes
  controlClass.register('starRating', controlStarRating);
  return controlClass;
});
