var moduleChoice = {
    init : function(option){
        option.click(function(){
            // Disable all option
            option.each(function() {
                if ($(this).val() == 0) {
                    $(this).attr('checked', true);

                    $(this).parent().parent().next('div').hide();
                }
            });

            // But not this option
            $(this).attr('checked', true);

            // Hide content if the option is set to false
            if ($(this).val() == 0) {
                $(this).parent().parent().next('div').hide();
            } else {
                $(this).parent().parent().next('div').show();
            }
        });
    }
};

var moduleOption = {
    init : function(option){
        option.click(function(){
            // If option is set to true, we display the content
            if ($(this).val() == 1) {
                $(this).parent().parent().next('div').show();
            } else {
                $(this).parent().parent().next('div').hide();
            }
        });
    }
};

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