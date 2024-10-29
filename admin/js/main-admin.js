jQuery( document ).ready( function( $ ) {
  $('#color-picker, #color-picker-hover').wpColorPicker();	
  $('#strategy').on('change', function(event){
	  var strategy = $(this).val();
	  if(strategy == 2){
		$(".maxselector_label").fadeOut('slow');
		$(".bg_selector").fadeOut('slow');
		$(".hv_selector").fadeOut('slow');
		$(".font_selector").fadeOut('slow');
		$(".size_selector").fadeOut('slow');		
	  }
	  else{
		$(".maxselector_label").fadeIn('slow');
		$(".bg_selector").fadeIn('slow');
		$(".hv_selector").fadeIn('slow');
		$(".font_selector").fadeIn('slow');
		$(".size_selector").fadeIn('slow');		
		
	  }
  });
	$('.safont').fontselect({placeholder: 'Default',})
	.on('change', function() {
	   //applyFont(this.value);
	});
	$('#resfont').on("click", function(event){
		event.preventDefault();
		$('.safont').trigger('setFont','');
		
	});
	$('#resfontsize').on("click", function(event){
		event.preventDefault();
		$("#fontsizeselector").val("");		
	});
	$('#resmaxheight').on("click", function(event){
		event.preventDefault();
		$("#maxheight").val("");		
	});	
	
});