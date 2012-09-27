$(function(){
    $('#email').focus();
    $('#hint').click(function(){
        $('#email').val('admin@example.com');
        $('#pwd').val('admin');
    });
});