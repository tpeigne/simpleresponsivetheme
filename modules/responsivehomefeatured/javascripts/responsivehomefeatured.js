$(window).load(function(){
    $('.delete').click( function() {
        var confirmation = confirm($(this).attr('title'));

        if(confirmation == true){
            return true;
        }

        return false;
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
        $('#id_product').val(item[1]);
    });

    $('#categories').find('td.position').each(function(i) {
        $(this).html(i+1);
    });

    // Dynamic ajax position update
    $('table.tableDnD').tableDnD({
        onDragStart: function(table, row) {
            originalOrder = $.tableDnD.serialize();
        },
        onDrop: function(table, row) {
            if (originalOrder != $.tableDnD.serialize()){
                var way = (originalOrder.indexOf(row.id) < $.tableDnD.serialize().indexOf(row.id))? 1 : 0;

                params = {
                    action: 'updatePositionHomeFeatured',
                    id_responsive_homefeatured: row.id,
                    way: way
                };

                var xhr = $.ajax({
                    type: 'POST',
                    url: urlAjaxModule + '?' + $.tableDnD.serialize(),
                    data: params,
                    success: function(data){
                        $('#categories').find('td.position').each(function(i) {
                            $(this).html(i+1);
                        });
                    }
                });
            }
        }
    });
});