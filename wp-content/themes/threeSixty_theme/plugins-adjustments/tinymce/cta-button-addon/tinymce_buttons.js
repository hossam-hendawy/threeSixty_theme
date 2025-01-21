(function () {
  tinymce.PluginManager.add('swpbtn', function (editor, url) {

    let imagesDir = siteurl + '/wp-content/themes/threeSixty_theme/blocks/theme_buttons/acf-images/';
    // Add Button to Visual Editor Toolbar
    editor.addButton('swpbtn', {
      title: 'CTA shortcode- Click button to add a Shortcode',
      cmd: 'swpbtn',
      image: url + '/button.svg',
    });
    editor.addCommand('swpbtn', function () {
      var selected_text = editor.selection.getContent({
        'format': 'html'
      });

      var editor_id = "'.wp-" + editor['acf']['data']['id'] + "-wrap'";
      var editor_id_x = "wp-" + editor['acf']['data']['id'] + "-wrap";

      var html = `<div id="wp-link-wrap" class="${editor_id_x} wp-core-ui has-text-field" style="display: block;" role="dialog"
aria-labelledby="link-modal-title">
<style>
.cta-label-image{
position: relative;
flex-basis: 33.3%;
width: 33.3%;
display: block;
margin-top: 10px;
}
.cta-label-image img{
padding: 5px;
width: 85%;
border: 4px solid transparent;
}
.images-wrapper{
display: flex;
flex-wrap: wrap;
align-items: center;
}


.cta-label-image input{
position: absolute;
top:0;
left: 0;
width: 100%;
height: 100%;
opacity: 0;
}
.cta-label-image input:checked + img{
border: 4px solid #359235;
}
.custom-inputs-wrapper{
display: flex;
justify-content: space-between;
}

.custom-input-wrapper{
width: 48%;
}

.custom-input-wrapper input{
width: 100% !important;
}
.custom-input-wrapper span{
display: block !important;
width: 100% !important;
max-width: 100% !important;
text-align: left !important;
}
</style>
   <form id="wp-link" tabindex="99999999999">
      <input type="hidden" id="_ajax_linking_nonce" name="_ajax_linking_nonce" value="f544b7c1df">
      <h1 id="link-modal-title">Insert/edit link</h1>
      <button onclick="jQuery(${editor_id}).remove();" type="button" id="wp-link-close"><span class="screen-reader-text">Close</span></button>
      <div id="link-selector">
        <div id="link-options">
          <p class="howto" id="wplink-enter-url">Enter the CTA URL</p>
          <div>
            <label><span>CTA URL</span><input id="cta_url" type="text" aria-describedby="wplink-enter-url"></label>
          </div>
           <div>
            <label><span>CTA Text</span><input id="cta_text" type="text" ></label>
          </div>
          <label style="margin-top: 15px;display: block;">
          <span></span>
             <input type="checkbox" id="cta_target">
              Open link in a new tab
          </label>
            <br>
          <div class="images-wrapper">
            <label class="cta-label-image">
                <input type="radio" class="cta_type" name="cta_type" value="btn-1">
                <img src="${imagesDir}btn-1.png" alt="">
            </label>
            <label class="cta-label-image">
                <input class="cta_type" type="radio" name="cta_type" value="btn-2">
                <img src="${imagesDir}btn-2.png" alt="">
            </label>
            <label class="cta-label-image">
                <input class="cta_type" type="radio" name="cta_type" value="btn-3">
                <img src="${imagesDir}btn-3.png" alt="">
            </label>
             <label class="cta-label-image">
                <input class="cta_type" type="radio" name="cta_type" value="btn-4">
                <img src="${imagesDir}btn-4.png" alt="">
            </label>
            <label class="cta-label-image">
                <input class="cta_type" type="radio" name="cta_type" value="btn-5">
                <img src="${imagesDir}btn-5.png" alt="">
            </label>
              <label class="cta-label-image">
                <input class="cta_type" type="radio" name="cta_type" value="btn-6">
                <img src="${imagesDir}btn-6.png" alt="">
            </label>
          </div>
          <div class="custom-inputs-wrapper">
          <div class="custom-input-wrapper">
            <label><span>Margin Top</span><input id="cta_mt" type="text" name="cta_mt" placeholder="px"></label>
          </div>
           <div class="custom-input-wrapper">
            <label><span>Margin Bottom</span><input id="cta_mb" type="text" name="cta_mb" placeholder="px"></label>
          </div>
          </div>

        </div>
      </div>
      <div class="submitbox">
         <div onclick="jQuery(${editor_id}).remove();" id="wp-link-cancel"><button type="button" class="button">Cancel</button></div>
         <div id="wp-link-update">
            <input  onclick="event.preventDefault(); let cta = new Event('newcta'); document.dispatchEvent(cta); jQuery(${editor_id}).remove(); document.removeEventListener('newcta', test);" id="wp-link-updatex" type="submit" value="Add CTA BTN" class="button button-primary" id="wp-link-submit" name="wp-link-submit">
         </div>
      </div>
   </form>
</div>
`;

      jQuery('#' + editor_id_x).prepend(html);
      // console.log('#' + editor_id_x);
      //console.log(editor);

      //if()

      function test() {
        //console.log('wwwwwww');
        document.removeEventListener("newcta", test);
        document.removeEventListener("newcta", this);
        document.removeEventListener("newcta", this.test);
        var url = jQuery('#cta_url').val();
        var target = jQuery('#cta_target:checked').val();
        var text = jQuery('#cta_text').val();
        var type = jQuery('input[name="cta_type"]:checked').val();
        var cta_mt = jQuery('#cta_mt').val();
        var cta_mb = jQuery('#cta_mb').val();
        var cta_theme_white = jQuery('#cta_theme_white');


        var open_column = "[cod_cta url='" + url + "' text='" + text + "' ";

        if (type != '' && type !== undefined) {
          open_column += "type='" + type + "' ";
        }

        if (target == 'on') {
          open_column += "target='" + target + "' ";
        }

        if (cta_mt !== '') {
          open_column += "margin_top='" + cta_mt + "' ";
        }

        if (cta_mb !== '') {
          open_column += "margin_bottom='" + cta_mb + "' ";
        }


        var close_column = ']';
        var return_text = open_column + close_column;

        editor.execCommand('mceReplaceContent', false, return_text);
        jQuery(editor_id).remove();
        var open_column = '';
        var close_column = '';
        var return_text = '';


      }

      document.addEventListener("newcta", test);
      //
      return;
    });

  });
})();
