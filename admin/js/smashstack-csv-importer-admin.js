(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

 	$(document).ready( function(){

        // upload csv on change
 		$('input[name="csv_input_file"]').on("change", function (e) {
 		    var file = $(this)[0].files[0];
 		    var upload = new Upload(file);

            //$('#progress-wrp').show();
 		    // maby check size or type here with upload.getSize() and upload.getType()

 		    // execute upload
 		    upload.doUpload();
 		});


        // clicking add field condition button
        $('.add_field_condition').click(function(e){            
            var c = parseInt( $(this).attr('data-count') );
            var n = c + 1;
            var p = $(this).prev();

            var clone = p.find('[data-field]:last-child').clone().appendTo(p);
            clone.attr('data-field', 'field'+ n);
            clone.find('.remove-condition').show();
            clone.find('input[type="text"]').val('');

            $(this).attr('data-count', n);

            e.preventDefault();
        });

        // clicking (x) remove button
        $(document).on('click', '.remove-condition', function(e){
            $(this).parent().remove();

            e.preventDefault();
        });



 	});

})( jQuery );

var Upload = function (file) {
    this.file = file;
};

Upload.prototype.getType = function() {
    return this.file.type;
};
Upload.prototype.getSize = function() {
    return this.file.size;
};
Upload.prototype.getName = function() {
    return this.file.name;
};
Upload.prototype.doUpload = function () {
    var that = this;
    var formData = new FormData();

    var $ = jQuery;

    var p_keys = $('input[name="p_fields[key]"]').map(function(){return $(this).val();}).get();
    var p_values = $('select[name="p_fields[value]"]').map(function(){return $(this).val();}).get();
    var c_keys = $('input[name="c_fields[key]"]').map(function(){return $(this).val();}).get();
    var c_values = $('select[name="c_fields[value]"]').map(function(){return $(this).val();}).get();

    var p_fields = toObject(p_keys, p_values);
    var c_fields = toObject(c_keys, c_values);


    // add assoc key values, this will be posts values
    formData.append("file", this.file, this.getName());
    formData.append("action", $('input[name="action"]').val());
    formData.append("post_type", $('input[name="post_type"]').val());
    formData.append("p_fields", JSON.stringify(p_fields));
    formData.append("c_fields", JSON.stringify(c_fields));


    $.ajax({
        type: "POST",
        url: script_obj.ajax_url,
        xhr: function () {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', that.progressHandling, false);
            }
            return myXhr;
        },
        success: function (data) { //console.log(data);
            var output = $.parseJSON(data);

            if( output.status == 'Success' ) {
                $('.csv-upload-status').find('p.import').removeClass('loading');
                $('.csv-upload-status').find('p.import').addClass('done');

                $('.csv-upload-status').append('<p class="msg-success">'+ output.message +'</p>');
            }
            else {
                for(var i=0;i < data['errors'].length;i++) {
                    $('.csv-upload-status').html('<p class="msg-error">'+ output.errors[i] +'</p>');
                }
            }
        },
        error: function (error) {
           $('.csv-upload-status').html('<p class="msg-error">'+ JSON.stringify(error) +'</p>')
        },
        async: true,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        timeout: 1200000
    });
};

Upload.prototype.progressHandling = function (event) {
	var $ = jQuery;
    var percent = 0;
    var position = event.loaded || event.position;
    var total = event.total;
    var progress_bar_id = "#progress-wrp";
    if (event.lengthComputable) {
        percent = Math.ceil(position / total * 100);
    }

    $('.csv-upload-status').html('<p class="uploading loading">Uploading CSV ('+ percent +'%)</p>');

    if( percent == 100 ) {
        setTimeout(function(){
            $('.csv-upload-status').find('p.uploading').removeClass('loading');
            $('.csv-upload-status').find('p.uploading').addClass('done');

            $('.csv-upload-status').append('<p class="import loading">Importing Data</p>');
        }, 300);
    }

};

function toObject(names, values) {
    var result = {};
    for (var i = 0; i < names.length; i++)
         result[names[i]] = values[i];
    return result;
}