$(window).load(function(){
    var imgOnline = '../img/admin/enabled.gif';
    var imgOffline = '../img/admin/disabled.gif';

    $("#add_homefeatured").click(function (){
        $("#informations_link").slideToggle("slow");

        return false;
    });

    $('.delete_homefeatured').click( function() {
        var lien = $(this);
        var confirmation = confirm(lien.attr('title'));

        if(confirmation == true){
            var xhr = $.ajax({
                type: 'POST',
                url: urlAjaxModule,
                data: 'idHomeFeatured='+lien.attr('id')+'&action=deleteHomeFeatured',
                success: function(data) {
                    $('#ajax_response').html(data).slideDown(500);
                    lien.parent().parent().remove();
                }
            });
        }

        return false;
    });

    $('.delete_homefeatured_product').click( function() {
        var lien = $(this);
        var confirmation = confirm(lien.attr('title'));

        if(confirmation == true){
            var xhr = $.ajax({
                type: 'POST',
                url: urlAjaxModule,
                data: 'idHomeFeaturedProduct='+lien.attr('id')+'&action=deleteHomeFeaturedProduct',
                success: function(data) {
                    $('#ajax_response').html(data).slideDown(500);
                    lien.parent().parent().remove();
                }
            });
        }

        return false;
    });

    $("a.editHomeFeatured").click(function () {
        $(this).next('form').submit();

        return false;
    });

    $('.toggle_sub_categories').click(function(){
        var idParent = $(this).parent().parent().attr('id');
        $('.'+idParent).toggle();

        return false;
    });

    function loadProductsCategory(){
        //load products for default category
        $('#product_ajax').html('Loading...');
        $.getJSON(urlAjaxModule,
        {idCategory : $('#id_category option:selected').val(), action : "getProductList"},
        function(data) {
            if(data == ""){
                $('#product').html('').hide();
                $('#submitAddHomeFeatured').hide();
                $('#product_ajax').html(msgProducts);
            }else{
                var options = '';
                for(i = 0 ; i < data.length ; i++){
                    options += '<option value="'+data[i]['id_product']+'">'+data[i]['name']+'</option>';
                }

                $('#product').html(options).show();
                $('#submitAddHomeFeatured').show();
                $('#product_ajax').html('');
            }
        });
    }

    $('#id_category').change(function(){
        loadProductsCategory();
    });

    loadProductsCategory();

    $('#categories').find('td.position').each(function(i) {
        $(this).html(i+1);
    });

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