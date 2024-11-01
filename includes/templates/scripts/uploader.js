jQuery(document).ready(function () {

    jQuery('#upload_image_button').click(function () {
        formfield = jQuery('#upload_image').attr('name');
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    window.send_to_editor = function (html) {
        var div = document.createElement('div');
        div.innerHTML = html;
        var firstImage = div.getElementsByTagName('img')[0];
        var imgSrc = firstImage ? firstImage.src : "";
        var rawImgSrc = firstImage ? firstImage.getAttribute("src") : "";
        html = '<form action=""  id="hidden-form-image" method="POST">' +
            '<input type="hidden" name="image_url" value="' + rawImgSrc + '">' +
            '</form>';
        jQuery('#hidden-form').append(html);
        jQuery('#hidden-form-image').submit();
        tb_remove();
    }
});