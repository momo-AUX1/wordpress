jQuery(document).ready(function($){
    var frame;
    $('#app_icon_upload_btn').on('click', function(e){
        e.preventDefault();

        if (frame) {
            frame.open();
            return;
        }

        frame = wp.media({
            title: 'Select or Upload Icon',
            button: {
                text: 'Use this icon'
            },
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#app_icon_id').val(attachment.id);
            $('#app_icon_preview').html('<img src="'+attachment.url+'" style="max-width:100px;max-height:100px;"/>');
        });

        frame.open();
    });
});
