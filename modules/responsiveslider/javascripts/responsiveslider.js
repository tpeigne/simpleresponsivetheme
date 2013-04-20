$(window).load(function(){
    var imgOnline = '../img/admin/enabled.gif';
    var imgOffline = '../img/admin/disabled.gif';

    $("#add-image").click(function () {
        $("#informations-image").slideToggle("slow");

        return false;
    });

    $("h3 a").click(function () {
        $(this).parent().next('div').slideToggle("slow");

        return false;
    });

    /** Form validation **/
    // $('#informations-image').validate();

    /** slide edition **/
    $("a.editSlide").click(function () {
        $(this).next('form').submit();

        return false;
    });

    $("a.deleteImage").click(function () {
        $('#deleteImageForm').submit();

        return false;
    });

    /** Ajax request to delete a slide **/
    $('.delete-image').click( function() {
        var lien = $(this);
        var confirmation = confirm(lien.attr('title'));

        if(confirmation == true){
            var xhr = $.ajax({
                type: 'POST',
                url: lien.attr("urlajax"),
                data: 'idSlide='+lien.attr('id')+'&action=deleteSlide',
                success: function(data) {
                    $('#ajax-response').html(data).slideDown(500);
                    lien.parent().parent().remove();
                }
            });
        }

        return false;
    });

    /** Ajax request to delete put online / offline a slide **/
    $('.online-slide').click( function() {
        var lien = $(this);
        var confirmation = confirm(lien.attr('title'));
        var actionOnline = lien.attr('actionOnline');

        if(confirmation == true){
            var xhr = $.ajax({
                type: 'POST',
                url: lien.attr("urlajax"),
                data: 'idSlide='+lien.attr('id')+'&action=onlineSlide&actionOnline='+actionOnline+'',
                success: function(data) {
                    if(actionOnline == 'putOffline'){
                        lien.children().attr('src', imgOffline);
                        lien.attr('actionOnline', 'putOnline');
                        $('#ajax-response').html(data).slideDown(500);
                    }else{
                        lien.children().attr('src', imgOnline);
                        lien.attr('actionOnline', 'putOffline');
                        $('#ajax-response').html(data).slideDown(500);
                    }
                }
            });
        }

        return false;
    });

    $('#slides').find('td.position').each(function(i) {
        $(this).html(i+1);
    });

    $('table.tableDnD').tableDnD({
        onDragStart: function(table, row) {
            originalOrder = $.tableDnD.serialize();
        },
        onDrop: function(table, row) {
            if (originalOrder != $.tableDnD.serialize()){
                params = {
                    action: 'updatePositionSlide',
                    id_slide: row.id
                };

                var xhr = $.ajax({
                    type: 'POST',
                    url: urlAjaxModule + '?' + $.tableDnD.serialize(),
                    data: params,
                    success: function(data){
                        $('#slides').find('td.position').each(function(i) {
                            $(this).html(i+1);
                        });
                    }
                });
            }
        }
    });

    //overview of a slide image
    $(".apercu-fancy").fancybox({});
});