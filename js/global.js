//@codekit-prepend "jquery-1.9.1.js";
//@codekit-prepend "jquery.mousewheel.js";
//@codekit-prepend "jquery.jscrollpane.js";
//@codekit-prepend "bootstrap.js";

jQuery(function(){
	//@codekit-prepend "timeline.js";
	//@codekit-prepend "overlay.js";
	//@codekit-prepend "home.js";

	//console log helper
	if (typeof console === "undefined") window.console = { log: function () {} };

	//automatically open window if there's a hash
	if (location.hash && (location.hash !== "#contact")) overlay.show(location.hash);

	//open overlays when hash link clicked
	$(window).on('hashchange', function() {
		if (location.hash && (location.hash !== "#contact")) {
			overlay.show(location.hash);
		} else {
			overlay.hide();
		}
	});

	//header
	jQuery("li.search").hover(function(){
		jQuery(this).find("input").first().focus();
	},function(){
		jQuery(this).find("input").first().blur();
	});
	
	//hide placeholder on focus
	jQuery("input").focus(function(){
		jQuery(this).attr("data-placeholder", jQuery(this).attr("placeholder"));
		jQuery(this).attr("placeholder", "");
	}).blur(function(){
		jQuery(this).attr("placeholder", jQuery(this).attr("data-placeholder"));
		jQuery(this).attr("data-placeholder", "");
	});
	
	jQuery("li.search i").click(function(){
		jQuery("input#search").val("");
	});
	
	//home insights
	jQuery("#home_insights .arrow a").click(function(e){
		e.preventDefault();
	});

	jQuery("#home_insights .insight a").click(function(e){
		e.preventDefault();
	});

	//initialize timeline if appropriate
	if (jQuery("body").hasClass("timeline")) timeline.init();

	//set browse page accordion
	jQuery("#browse").on("click", "h3", function(){
		jQuery(this).parent().find("ul").slideToggle();
		var $icon = jQuery(this).find("i").first();
		if ($icon.hasClass("icon-plus-circled")) {
			$icon.removeClass("icon-plus-circled").addClass("icon-minus-circled");
		} else {
			$icon.removeClass("icon-minus-circled").addClass("icon-plus-circled");
		}
	});

	//set browse page links
	jQuery("#browse .header a").click(function(e){
		e.preventDefault();
		jQuery("#browse .header a").removeClass("active");
		jQuery(this).addClass("active");
		jQuery.post("/wp-admin/admin-ajax.php", {
			action : 'browse',
			type : jQuery(this).html().toLowerCase() 
		}, function(data) {
			jQuery("#browse .content").html(data);
		});
	});

	//regular jscrollpane
	jQuery('.scroll-pane').jScrollPane();

	//era page infographic scrollpane, has to be done differently
	var element = jQuery(".infographics .inner div.infographic_scroller")
		.jScrollPane()
		.bind('jsp-scroll-x', function(event, scrollPositionX, isAtLeft, isAtRight) {
			if (isAtLeft) {
				jQuery(".infographics a.arrow.left").hide();
			} else {
				jQuery(".infographics a.arrow.left").show();
			}
			if (isAtRight) {
				jQuery(".infographics a.arrow.right").hide();
			} else {
				jQuery(".infographics a.arrow.right").show();
			}
		});
	var interval = false;
	jQuery(".infographics a.arrow").hover(function(){
		var increment = (jQuery(this).hasClass("left")) ? -7 : 7;
		interval = setInterval(function(){
			element.data('jsp').scrollByX(increment);
		}, 10);
	}, function(){
		clearInterval(interval);
	});
	
	//map page
	if (jQuery("body").hasClass("maps")) {
		//description box
		jQuery("div#description a.close").click(function(e){
			jQuery(this).parent().toggleClass("minimized");
			e.stopPropagation();
		});
		jQuery("body").on("click", "div#description.minimized", function(){
			jQuery(this).toggleClass("minimized");
		});
	
		jQuery("#slider li.progressive").addClass("active");
		
		jQuery("#slider li").click(function(){
			if (jQuery(this).hasClass("active")) return;
			var old_era = jQuery("#slider li.active").attr("class").replace(" first", "").replace(" active", "").replace(" last", ""); //HACK
			var old_id = jQuery(".mapwrapper." + old_era).find(".map").attr("id");
			var center = window[old_id].getCenter();
			var zoom = window[old_id].getZoom();
			//alert(center.lat());
			jQuery("#slider li.active").removeClass("active");
			var new_era = jQuery(this).attr("class").replace(" first", "").replace(" last", ""); //HACK
			jQuery(this).addClass("active");
			jQuery(".mapwrapper").hide();
			jQuery(".mapwrapper." + new_era).show();
			var new_id = jQuery(".mapwrapper." + new_era).find(".map").attr("id");
			google.maps.event.addListenerOnce(window[new_id], 'bounds_changed', function() {
				window.console.log('bounds changed');
		    	this.setCenter(center);
		    	this.setZoom(zoom);
			});
			google.maps.event.trigger(window[new_id], 'resize');
	    	window[new_id].setCenter(center);
	    	window[new_id].setZoom(zoom);
		});
	}

});