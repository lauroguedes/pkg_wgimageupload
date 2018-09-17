/**
 * @author          Andy Hickey <andy@netamity.com> customized by Lauro W. Guedes <leowgweb@gmail.com>
 * @link            http://www.leowgweb.com.br
 * @copyright       Copyright © 2018 leowgweb - All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

(function($) {
	$(document).ready(function(){
		$(function() {
			start.init();
		});
		var start = {
			init : function() {
                start.loadInputFile();
                start.removeImage();
			},
			loadInputFile : function() {
				$("input[type=file]").change(function() {

                    //picking up the element to upload
                    var getEl = $(this).attr('id');
                    var fieldFile = $('#' + getEl).get(0);
            
                    var myFormData = new FormData();
                    myFormData.append('pictureFile', fieldFile.files[0]);
                    /*
                    * inserts the information referring
                    * to the unique name of the field so that
                    * it can be sent in the requisition
                    * */
                    myFormData.append('referenceField', getEl);
            
                    $.ajax({
                        url: window.location.origin+"/index.php?option=com_ajax&plugin=wgimageupload&format=raw",
                        type: "POST",
                        processData: false, // important
                        contentType: false, // important
                        //dataType : 'json',
                        data: myFormData,
                        beforeSend: function () {
                            $('#remove-image' + getEl).fadeOut();
                            $('#setimgval' + getEl).val('');
                            $('#iu_result' + getEl).html('');
                            $('#iu_error' + getEl).html('');
                            $("#iu_result" + getEl).addClass('set-loading');
                            $("#" + getEl + " + label").addClass('disabled');
                            $("#" + getEl).prop('disabled', true);
                        },
                        complete: function () {
                            $("#" + getEl).prop('disabled', false);
                            $("#" + getEl + " + label").removeClass('disabled');
                            $("#iu_result" + getEl).removeClass('set-loading');
                            $('#remove-image' + getEl).fadeIn();
                            start.removeImage();
                        }
                    })
                    .done(function (data) {
                        var obj = JSON.parse(data);
                        if(obj.error)
                        {
                            $("#iu_error" + getEl).html('<span>'+obj.error+'</span>').css('color', 'red');
                            $("#iu_result" + getEl).html('<i class="icon-warning text-error"></i>');
                            $("#" + getEl).val("");
                        }
                        else if(obj.relpath)
                        {
                            var src = '<img src="' + obj.relpath + '" /><i id="remove-image'+ getEl +'" style="display: none" title="Deletar Imagem" class="icon-remove"></i>';
                            $("#iu_result" + getEl).html(src);
        
                            $("#iu_error" + getEl).html('<i class="icon-checkmark-2"></i>').css('color', 'green');
        
                            $("#setimgval" + getEl).val(obj.relpath);
                        }
                    })
                    .fail(function (jqXHR, textStatus, msg) {
                        $("#iu_error" + getEl).html('<span>'+msn+'</span>').css('color', 'red');
                        $("#iu_result" + getEl).html('<i class="icon-warning text-error"></i>');
                        $("#" + getEl).val("");
                    })
                });
            },
            removeImage : function() {
                $('.icon-remove').on('click', function () {
                    var el = $(this).parent().attr('id');
                    var getEl = el.slice(9);
                    $('#setimgval' + getEl).val('');
                    $('#iu_result' + getEl).html('');
                    $('#iu_error' + getEl).html('');
                    $(this).fadeOut();
                });
            }
		};
	});
})(jQuery);