$(window).load(function() {
    // Check and uncheck radio buttons automatically
    moduleChoice.init($('.link-type .link-choice'));
    moduleOption.init($('.link-option'));

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