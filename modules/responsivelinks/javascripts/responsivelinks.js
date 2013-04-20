$(window).load(function(){
    var imgOnline = '../img/admin/enabled.gif';
    var imgOffline = '../img/admin/disabled.gif';

    $("#add_link").click(function (){
        $("#informations_link").slideToggle("slow");

        return false;
    });

    //if category link
    $('input[type=radio][name=iscategory]').click(function(){
        if($('#iscategory_on:checked').length == 1){
            $('.category_block').show();
            $('#iscms_on, #iscustom_on, #isproduct_on').attr('checked', false);
            $('#iscms_off, #iscustom_off, #isproduct_off').attr('checked', true);
        }

        if($('#iscategory_off:checked').length == 1){
            $('.category_block').hide();
        }

        $('.cms_block, .custom_block, .product_block').hide();
    });

    //if cms link
    $('input[type=radio][name=iscms]').click(function(){
        if($('#iscms_on:checked').length == 1){
            $('.cms_block').show();
            $('#iscategory_on, #iscustom_on, #isproduct_on').attr('checked', false);
            $('#iscategory_off, #iscustom_off, #isproduct_off').attr('checked', true);
        }

        if($('#iscms_off:checked').length == 1){
            $('.cms_block').hide();
        }

        $('.category_block, .custom_block, .product_block').hide();
    });

    //if product link
    $('input[type=radio][name=isproduct]').click(function(){
        if($('#isproduct_on:checked').length == 1){
            $('.product_block').show();
            $('#iscategory_on, #iscustom_on, #iscms_on').attr('checked', false);
            $('#iscategory_off, #iscustom_off, #iscms_off').attr('checked', true);
        }

        if($('#isproduct_off:checked').length == 1){
            $('.product_block').hide();
        }

        $('.category_block, .custom_block, .cms_block').hide();
    });

    //if custom link
    $('input[type=radio][name=iscustom]').click(function(){
        if($('#iscustom_on:checked').length == 1){
            $('.custom_block').show();
            $('#iscms_on, #iscategory_on, #isproduct_on').attr('checked', false);
            $('#iscms_off, #iscategory_off, #isproduct_off').attr('checked', true);
        }

        if($('#iscustom_off:checked').length == 1){
            $('.custom_block').hide();
        }

        $('.category_block, .cms_block, .product_block').hide();
    });

    //if parent link
    $('input[type=radio][name=isparent]').click(function(){
        if($('#isparent_on:checked').length == 1){
            $('.parent_block').show();
        }

        if($('#isparent_off:checked').length == 1){
            $('.parent_block').hide();
        }
    });

    $('.toggle_sub_categories').click(function(){
        var idParent = $(this).parent().parent().attr('id');
        $('.'+idParent).toggle();

        return false;
    });

    /** Ajax request to delete a slide **/
    $('.delete_link').click( function() {
        var lien = $(this);
        var confirmation = confirm(lien.attr('title'));

        if(confirmation == true){
            var xhr = $.ajax({
                type: 'POST',
                url: lien.attr("urlajax"),
                data: 'idLink='+lien.attr('id')+'&action=deleteLink',
                success: function(data) {
                    $('#ajax_response').html(data).slideDown(500);
                    var idLink = lien.parent().parent().attr('id').split('node-');
                    //delete all the sub links for this link
                    deleteSubLinks(idLink[1]);
                    lien.parent().parent().remove();
                }
            });
        }

        return false;
    });

    function deleteSubLinks(idLink){
        //get all sub links from parent
        var subLinks = $('.child-of-node-'+idLink);
        //foreach sub link, delete the link and his sublinks
        $(subLinks).each(function(){
            var idSubLink = $(this).attr('id').split('node-');
            deleteSubLinks(idSubLink[1]);
            $(this).remove();
        });
    }

    /** link edition **/
    $("a.editLink").click(function () {
        $(this).next('form').submit();

        return false;
    });

    $('#links').find('td.position').each(function(i) {
        $(this).html(i+1);
    });

    $('table.tableDnD').tableDnD({
        onDragStart: function(table, row) {
            originalOrder = $.tableDnD.serialize();
        },
        onDrop: function(table, row) {
            if (originalOrder != $.tableDnD.serialize()){
                params = {
                    action: 'updatePositionLinks',
                    id_link: row.id
                };

                // console.log(row);

                var xhr = $.ajax({
                    type: 'POST',
                    url: urlAjaxModule + '?' + $.tableDnD.serialize(),
                    data: params,
                    success: function(data){
                        $('#links').find('td.position').each(function(i) {
                            $(this).html(i+1);
                        });
                    }
                });
            }
        }
    });

    $("#links").treeTable({
        clickableNodeNames: true
    });
});