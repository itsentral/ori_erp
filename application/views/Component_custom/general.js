
$(".numberOnly").on("keypress keyup blur",function (event) { 
	if ((event.which < 48 || event.which > 57 ) && (event.which < 46 || event.which > 46 )) {
		event.preventDefault();
	}
});