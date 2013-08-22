var tableOffset = $("#RP_Main_FixTable").offset().top;
var $header = $("#RP_Main_FixTable > thead").clone();
var $fixedHeader = $("#header-fixed").append($header);

$(window).bind("scroll", function() {
    var offset = $(this).scrollTop();

    if (offset >= tableOffset && $fixedHeader.is(":hidden")) {
        $fixedHeader.show();
    }
    else if (offset < tableOffset) {
        $fixedHeader.hide();
    }
});

jQuery(function ($) {
	// Load dialog on page load
	//$('#basic-modal-content').modal();

	// Load dialog on click
	$('.embedtweetbutton input[type=submit][class=twitter-popup]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/twitter-controller.php", data, function(data) {
            $.modal(data);
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedtweetbutton input[type=submit][class=oneclick]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/twitter-controller.php", data, function(data) {
            $('#RP_Status').html(data);
            $('#RP_Status').animate({width: 'toggle'}).delay(500).animate({width: 'toggle'});
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedfbsharebutton input[type=submit][class=fbshare-popup]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/facebook-controller.php", data, function(data) {
            $.modal(data);
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedfbsharebutton input[type=submit][class=oneclick]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/facebook-controller.php", data, function(data) {
            $('#RP_Status').html(data);
            $('#RP_Status').animate({width: 'toggle'}).delay(500).animate({width: 'toggle'});
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedgpsharebutton input[type=submit][class=gpshare-popup]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/googleplus-controller.php", data, function(data) {
            $.modal(data);
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedgpsharebutton input[type=submit][class=oneclick]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/googleplus-controller.php", data, function(data) {
            $('#RP_Status').html(data);
            $('#RP_Status').animate({width: 'toggle'}).delay(500).animate({width: 'toggle'});
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedtblogbutton input[type=submit][class=tblog-popup]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/tumblr-controller.php", data, function(data) {
            $.modal(data);
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedtblogbutton input[type=submit][class=oneclick]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/tumblr-controller.php", data, function(data) {
            $('#RP_Status').html(data);
            $('#RP_Status').animate({width: 'toggle'}).delay(500).animate({width: 'toggle'});
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedpinbutton input[type=submit][class=pin-popup]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/pinterest-controller.php", data, function(data) {
            $.modal(data);
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
	
	$('.embedpinbutton input[type=submit][class=oneclick]').click(function (e) {
	    var data = $(this).closest("form").serializeArray();
        data.push({name: 'action', value: $(this).val()});
        $.get("../module/pinterest-controller.php", data, function(data) {
            $('#RP_Status').html(data);
            $('#RP_Status').animate({width: 'toggle'}).delay(500).animate({width: 'toggle'});
        });
        $(this).closest("form").find("input[type=submit]").attr("disabled", true);
		return false;
	});
});
