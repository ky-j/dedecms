$(function(){
    var mid= $("#mid").val(); 
    $.ajax({
      type: "GET",
      url: "?ct=myask&ac=reply&mid="+mid,
      dataType: "json",
      success : function(data){
             $('#FeedText').empty();
    		  var html = '<div class="newarticlelist"><ul>';
    		  $.each( data  , function(commentIndex, comment) {
    			   html += '<li><span>' + comment['senddate'] + '</span><a href="' + comment['htmlurl'] + '" target="_blank">' + comment['title'] + '</a></li>';
    		  })
    		 html +="</ul></div>";
    		 $('#FeedText').html(html);
      }
    }); 
    
    $('#answer').click(function() {
        $.ajax({
    	  type: "GET",
    	  url: "?ct=myask&ac=reply&mid="+mid,
    	  dataType: "json",
    	  success : function(data){
    	         $('#FeedText').empty();
    			  var html = '<div class="newarticlelist"><ul>';
    			  $.each( data  , function(commentIndex, comment) {
    				   html += '<li><span>' + comment['senddate'] + '</span><a href="' + comment['htmlurl'] + '" target="_blank">' + comment['title'] + '</a></li>';
    			  })
    			 html +="</ul></div>";
    			 $('#FeedText').html(html);
    			 $("#answer").addClass("thisTab");
				 $("#ask").removeClass("thisTab");
    	  }
    	});
    });

    $('#ask').click(function() {
        $.ajax({
    	  type: "GET",
    	  url: "?ct=myask&ac=ask&mid="+mid,
    	  dataType: "json",
    	  success : function(data){
    	         $('#FeedText').empty();
    			  var html = '<div class="newarticlelist"><ul>';
    			  $.each( data  , function(commentIndex, comment) {
    				   html += '<li><span>' + comment['senddate'] + '</span><a href="' + comment['htmlurl'] + '" target="_blank">' + comment['title'] + '</a></li>';
    			  })
    			 html +="</ul></div>";
    			 $('#FeedText').html(html);
    			 $("#ask").addClass("thisTab");
				 $("#answer").removeClass("thisTab");
    	  }
    	});
    });
})