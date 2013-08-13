$(window).load(function(){
    $('.button.dropdown').click(function (){
        $('#'+$(this).attr('section')).slideToggle('slow');
    });
});