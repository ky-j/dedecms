<!--
$(document).ready(function()
{
	$.ajax({type: 'POST',url: "index.php",
	data: "ct=search&ac=check&title="+$("#title").val(),
	dataType: 'html',
	success: function(result){$("#_title").html(result);}}); 
});
-->