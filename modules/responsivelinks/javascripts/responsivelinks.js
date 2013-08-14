$(window).load(function() {
    // Check and uncheck radio buttons automatically
    $('.link-type input.link-option').click(function(){
        // Disable all option
        $('.link-type input.link-option-off').each(function() {
            $(this).attr('checked', true);

            $(this).parent().parent().next('div').hide();
        });

        // But not this option
        $(this).attr('checked', true);

        // Show the element with link type
        if ($(this).parent().children('input.link-option-off:checked').length == 1) {
            $(this).parent().parent().next('div').hide();
        } else {
            $(this).parent().parent().next('div').show();
        }
    });

    // Check option click for parent link
    $('input[type=radio][name=isparent]').click(function(){
        if($('#isparent_on:checked').length == 1){
            $('.parent_block').show();
        }

        if($('#isparent_off:checked').length == 1){
            $('.parent_block').hide();
        }
    });

    // Edition of a link
    /*$("a.editLink").click(function () {
        $(this).next('form').submit();

        return false;
    });*/

    $('#links').find('td.position').each(function(i) {
        $(this).html(i+1);
    });

    // Ajax request for product search
    $('#product_auto')
    .autocomplete('ajax_products_list.php', {
        minChars: 1,
        autoFill: true,
        max:20,
        matchContains: true,
        mustMatch:true,
        scroll:false,
        cacheLength:0,
        multipleSeparator:'||',
        formatItem: function(item) {
            return item[1]+' - '+item[0];
        }
    }).result(function(event, item){
        $('#product').val(item[1]);
    });

    // Dynamic ajax position update
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

                var xhr = $.ajax({
                    type: 'POST',
                    url: urlAjaxModule + '?' + $.tableDnD.serialize(),
                    data: params,
                    success: function(){
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