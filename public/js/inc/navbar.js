$(document).ready(function(){
    $('#notificationsDropdown').on('show.bs.dropdown', function () {
        console.log('test');
        if($('#notificationCounter').length)
        {
            $('#notificationCounter').remove();
    
            $.post($(this).data('url'), function(data){
                if(data.success != true)
                {
                    console.log("Something went wrong!");
                }
            });
        }
    });
    $('#notificationsDropdown').on('hide.bs.dropdown', function () {
        $('.notification-item').remove();
        $('#noNotificationsMessage').removeClass('d-none');
    });
});