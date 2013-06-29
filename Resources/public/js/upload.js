$(document).ready(function()
{
    Dropzone.options.dropzone = {
        paramName: "wurstpress_document[file]", // The name that will be used to transfer the file
        accept: function(file, done)
        {
            file.previewTemplate.querySelector(".dz-details").appendChild(Dropzone.createElement('<div class="image-remove"><a class="image-reload-trigger" href="' + top.location.href + '">✖</a></div>'));
            done();
        }
    };

    $('#dropzone').on('click', 'a.image-remove-trigger', function(event)
    {
        var $div = $(this).parents('div.dz-preview');
        $.get($(this).attr('href'), function()
        {
            $div.remove();
        });
        event.preventDefault();
    });

    $('#dropzone').on('click', 'a.image-reload-trigger', function(event)
    {
        window.location.reload();
        event.preventDefault();
    });
});