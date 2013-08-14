$(window).load(function() {
    $('.button.dropdown').click(function (){
        $('#'+$(this).attr('section')).slideToggle('slow');
    });

    //Delete action
    $('.delete').click( function() {
        var confirmation = confirm($(this).attr('title'));

        if(confirmation == true){
            return true;
        }

        return false;
    });
});