Date.prototype.addHours= function(h){
    this.setHours(this.getHours()+h);
    return this;
}

$(document).ready(function(){
  //
  //
  // This Part is used for loading more messages
  //
  //
  let offset_counter = 7;
  let url = $("#loadMessages").attr('data-api-url');
  let userId = $("#loadMessages").attr('data-user-id');
  let reportedById = $("#loadMessages").attr('data-reported-by-id');
  let conversationId = $("#loadMessages").attr('data-conversation-id')

  $("#loadMessages").on("click", function(){
    console.log("click");
    $.get(url + conversationId + "/" + offset_counter, function(data){
      console.log(data);
      // If 8 messages have been returned, we just remove the last one
      // Otherwise we remove the request button for more messages
      if(data["messages"].length > 7){
        data["messages"].shift();
        // Increase the offset counter for the next time
        offset_counter += 7;
      }else{
        $("#loadMessages").remove();
      }

      data["messages"].forEach(function(message){
        let conversationPartner = "";
        let pullRight = "";
        if(reportedById == message.userId){
          pullRight = "pull-right";
        }

        let html = '<div class=\"card mb-3 col-10 ' + pullRight + ' \"><div class=\"card-body\">' + message.body + '<p class=\"card-text\"><small class=\"text-muted pull-left\">' + message.userName + '</small><small class=\"text-muted pull-right\">' + message.createdAt + '</small></p></div></div>';

        $("#AddOldMessagesHere").after(html);
      });
    })
  });



  $("select.form-control").each(function(elem){
    let elemPrefix = $(this).data('prefix');
    console.log($("#" + elemPrefix + "datePicker"));
    $("#" + elemPrefix + "datePicker").datetimepicker({minDate: new Date().addHours(1)});
  });
  $("html").click(function(){
     var container = $('.bootstrap-datetimepicker-widget');
     container.parent().datetimepicker('hide');
  });



  $("select").on("change", function(){
    let prefix = $(this).data('prefix');

    if($(this).val() == "") {
      $("#" + prefix + "inputGroup").addClass('d-none');
    }else {
      $("#" + prefix + "inputGroup").removeClass('d-none');
    }

    if($(this).val() == "temporaryBan") {
      $("#" + prefix + "datePickerGroup").removeClass('d-none');
    }else {
      $("#" + prefix + "datePickerGroup").addClass('d-none');
    }
  });

  $("#messages").scrollTop($("#messages")[0].scrollHeight);
  $(document).scrollTop($("#lastMessage").offset().top);
});



$(document).ajaxError(function() {
  alert('Something went wrong, you might want to consider reloading the page');
});
