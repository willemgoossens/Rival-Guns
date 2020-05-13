$(document).ready(function(){
  if($(".border-info:first").length){
    $("#messages").scrollTop($(".border-info:first").offset().top);
    $(document).scrollTop($(".border-info:first").offset().top);
  }else{
    $("#messages").scrollTop($("#messages").height());
    $(document).scrollTop($(document).height());
  }

  $(":checkbox#other").on("click", function(){
    $("#otherExplanation").prop("disabled", (_, val) => !val);
  });

  $(":checkbox").on("click", function(){
      var numberOfChecked = $('input.form-check-input:checked').length;
      if(numberOfChecked > 0){
        $("#reportConversation").prop("disabled", false);
      }else{
        $("#reportConversation").prop("disabled", true);
      }
  });

  //
  //
  // This Part is used for loading more messages
  //
  //
  let offset_counter = 7;
  let url = $("#loadMessages").attr('data-api-url');
  let userId = $("#loadMessages").attr('data-user-id');
  let conversationId = $("#loadMessages").attr('data-conversation-id');

  $("#loadMessages").on("click", function(){
    console.log("click");
    $.get(url + "/" + conversationId + "/" + offset_counter,
    function(data){
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
        console.log("Rendering ID: " + message.id);
        let unread1 = "";
        let unread2 = "";
        if(message.unread == 1 && message.userId != userId){
          unread1 = "border-info";
          unread2 = "text-info";
        }

        let conversationPartner = "";
        let pullRight = "";
        if(userId != message.userId){
          conversationPartner = "<small class=\"text-muted pull-left\">" + message.name + "</small>";
        }else{
          pullRight = "pull-right";
        }

        let html = '<div class=\"card mb-3 col-10 ' + pullRight + unread1 + ' \"><div class=\"card-body ' + unread2 + '\">' + message.body + '<p class=\"card-text\">' + conversationPartner + '<small class=\"text-muted pull-right\">' + message.createdAt + '</small></p></div></div>';

        $("#AddOldMessagesHere").after(html);
      });
    })
  });
});

$(document).ajaxError(function() {
  alert('Something went wrong, you might want to consider reloading the page');
});
