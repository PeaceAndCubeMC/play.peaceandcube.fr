$(function() {
	var windowWidth = $(".slider").width();
	var windowHeight = $(".slider").height();
	var i = 0;
	var panelAmount = $(".slider-panel").length;
	
	// Give the panels a fitting H/W
	$(".slider-panel").css("width", windowWidth);
	$(".slider-panel").css("height", windowHeight);
	
	// Click on "Right" to move forward
		$(".right").click(function(){
		
			i+=1;
			
			if (i < 0 ){
				i = panelAmount - 1;
			}
			
			if (i >= panelAmount) {
				i = 0;
			}
		
			var pos=(i*windowWidth);
			$(".slider-panel-set").css("left", -pos + "px");
			});
	
		// Click on "Left" to move backward
		$(".left").click(function(){
			
			i-=1;
			
			if (i < 0 ){
				i = panelAmount - 1;
			}
			
			if (i >= panelAmount) {
				i = 0;
			}
			
			var pos=(i*windowWidth);
			$(".slider-panel-set").css("left", -pos + "px");
			});
	
	// Attempted Mobile Swipe Alternative
	
	// Swipe Forward
		$(".slider-panel-set").on("swipeleft", function(event){
		
			i+=1;
			
			if (i < 0 ){
				i = panelAmount - 1;
			}
			
			if (i >= panelAmount) {
				i = 0;
			}
		
			var pos=(i*windowWidth);
			$(".slider-panel-set").css("left", -pos + "px");
			});
	
	// Swipe Backward
			$(".slider-panel-set").on("swiperight", function(event){
			
			i-=1;
			
			if (i < 0 ){
				i = panelAmount - 1;
			}
			
			if (i >= panelAmount) {
				i = 0;
			}
			
			var pos=(i*windowWidth);
			$(".slider-panel-set").css("left", -pos + "px");
			});
	
});