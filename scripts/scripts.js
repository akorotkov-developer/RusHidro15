$(function(){	
	
	$('.landing-menu-item').on('click',function(){
		$('.landing-menu-item').removeClass('active');
		$(this).addClass('active');
		lmOffset = $(this).position();		
		$('.landing-menu-cursor').css('top',lmOffset.top+2);
		
		$('.landing-page').removeClass('active').eq($(this).index()-1).addClass('active');
		$('.container').attr('class','container page' + $(this).index());
		$('#dynasty-page').find('.dynasty-list-item').first().trigger('click');
		$('#good-deeds').find('.dynasty-list-item').first().trigger('click');
		$('#historyhydro').find('.dynasty-list-item').first().trigger('click');
		fleXenv.updateScrollBars();
	})	
	
	setTimeout(function(){ fleXenv.updateScrollBars() },2000);	
	
	infoslider = $("#info-block-slider").owlCarousel({
		//items: 1, dots: false, autoplay: true, nav:false, animateIn: 'fadeIn', animateOut: 'fadeOut', loop: true
		items: 1, dots: true, autoplay: true, nav:false, loop: true
	});
	
	infoslider.on('changed.owl.carousel', function(event) {		
		eind = $("#info-block-slider").find('.owl-item').eq(event.item.index).find('.info-block-slider_item').data('image');
		$('#index-illustration').find('.illustration').removeClass('active');
		$('#'+eind).addClass('active');		
	})	
	
	dateslider = $("#date-block-slider").owlCarousel({
		//items: 1, dots: false, autoplay: true, nav:false, animateIn: 'fadeIn', animateOut: 'fadeOut', loop: true, mouseDrag: false, autoplayTimeout: 6000
		items: 1, dots: false, autoplay: true, nav:false, loop: true, mouseDrag: false, autoplayTimeout: 6000
	});
	
	/*====history===*/
	historySlider = $("#history-slider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false 
	});
	
	hdw_trigger = 1;
	historySlider.on('changed.owl.carousel', function(event) {
		lp = $('#landing-page-bg').find('.landing-page-bg');
		lp.removeClass('active').eq(event.item.index).addClass('active');	
		$('.history-dot').removeClass('active');
		dot = $('.history-dot').eq(event.item.index);
		dot.addClass('active');
		tp = dot.position();
		tw = dot.width();
		if(!dot.hasClass('start')) $('.history-cursor').css('left',tp.left+(tw/2)-12);
		if(dot.hasClass('start')) $('.history-cursor').css('left',0);
		
		hdw = $('.history-dots-wrapper').width();		
		if((tp.left+tw/2)>=hdw) { if(hdw_trigger) { $('.history-dots-nav-next').trigger('click'); hdw_trigger = 0 } } 
		else { if(!hdw_trigger) { $('.history-dots-nav-prev').trigger('click'); hdw_trigger = 1 } } 		
		
		$("#history-slider").find('.flexcroll').each(function(){
			DivElement = $(this)[0];
			DivElement.fleXcroll.setScrollPos(false,0);	
		})
		
	})	
	
	$('.history-dots-nav-next').on('click',function(){
		if($(this).hasClass('active')) {
			$('.history-dots-over-scroll').addClass('scrolled');
			$('.history-dots-nav-next').removeClass('active');
			$('.history-dots-nav-prev').addClass('active');
			hdw_trigger = 0
		}
	})
	
	$('.history-dots-nav-prev').on('click',function(){
		if($(this).hasClass('active')) {
			$('.history-dots-over-scroll').removeClass('scrolled');
			$('.history-dots-nav-next').addClass('active');
			$('.history-dots-nav-prev').removeClass('active');
			hdw_trigger = 1
		}	
	})
	
	$('.history-dot').on('click',function(){
		$('#history-slider').find('.owl-dot').eq($(this).index()).trigger('click')
	})
	/*===eof_history===*/
	
	
	$('.scroll-next').on('click',function(){
		if(!$('.container').hasClass('page5')) $('.landing-menu-item.active').next().click();
		else $('.landing-menu-item').first().click();
	})
	
	$('#status-page').on('click',function(){
		$('#status-select').slideUp();
	})
	
	$('.status-select-title').on('click',function(e){
		e.stopPropagation();
		$('#status-select').slideToggle();
		$(this).toggleClass('active')
	})
	
	
	
	stationsSlider = $("#stations-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 20, slideBy: 3,
		responsive : {
			0 : { items : 6 },	
			1600 : { items : 8 },	
			1800 : { items : 9 }
		}
	});
	
	stationsMap = $('#stations-map');
	
	$("#stations-list").find('.stations-list-item').on('mouseenter',function(){
		bid = $(this).data('id');
		trg = $('#'+bid);
		tw = stationsMap.width()/2;
		tpos = trg.position();		
		if(tw<tpos.left) trg.addClass('toright');
		trg.addClass('active');
	})
	
	$("#stations-list").find('.stations-list-item').on('mouseleave',function(){
		$('#'+bid).removeClass('active');
	})
	
	$("#stations-list").find('.stations-list-item').on('click',function(){
		bid = $(this).data('id');
		trg = $('#'+bid);
		trg.find('a').trigger('click')
	})
	
	$('.stations-map-points-item').on('mouseenter',function(){
		tw = stationsMap.width()/2;
		tpos = $(this).position();
		if(tw<tpos.left) $(this).addClass('toright');		
	})
	
	$('.fancybox').fancybox();
	
	stationsImages = $("#stationsPopupImages").owlCarousel({
		items: 1, dots: false, autoplay: false, nav:true, animateIn: 'slideInDown', animateOut: 'slideOutDown'
	});
	
	stationsPopup = $('.stations-popup');
	
	$('.stations-map-link').on('click',function(e){
		e.preventDefault();
		$('#stationsPopup').html('');		
		$('#stationsPopup').addClass('active');
		url = $(this).attr('href');		
		
		$.ajax({
			type: "POST", 
			url: url, 						
			success: function(data) {
				galleryIndex = 1;	
				$('#stationsPopup').html(data);
				$('#stationsPopup').find('a.fancybox').fancybox();				
			}
		});			
		
		
	})
	
	$('body').on('click','.stations-popup-close, .stations-popup-right-close',function(){
		stationsPopup.removeClass('active');
	})
	
	$(document).on('keydown', function(e){ 	
		if (e.keyCode == 27) { $('body').find('.stations-popup').removeClass('active')}; 
		if (e.keyCode == 40) { $('.scroll-next').trigger('click'); }
		if (e.keyCode == 38) { $('.landing-menu-item.active').prev().click(); }		
	})
	
	$('.landing-menu').on('mousewheel', function(e) {		
		if( e.deltaY<0 ) $('.scroll-next').trigger('click');
		if( e.deltaY>0 ) $('.landing-menu-item.active').prev().click();
	})	
	
	stationsImages.on('changed.owl.carousel', function(event) {
		ic = event.item.index+1;
		if (ic<10) { ic = "0"+ic }
		$('#stationsIndex').text(ic)
	})
	
	stationsImg = $('#stationsImg');
	
	$('.status-select-drop-item').on('click',function(e){
		e.preventDefault();
		$('.status-select-drop-item').removeClass('active');
		$(this).addClass('active');
		dtype = $(this).data('type');
		if(dtype) {
			$('.stations-map-points-item').addClass('disabled');
			$('#stations-map').find('.type'+dtype).removeClass('disabled');
			if(dtype==11 || dtype==4 ) $('#stations-map').find('.type'+12).removeClass('disabled');
		} else {
			$('.stations-map-points-item').removeClass('disabled');
		}	
		$('.status-select-title').toggleClass('active').find('span').text($(this).text())
		if(dtype==11) { stationsImg.attr('src','/styles/img/map_dv.jpg') } 
		else if (dtype==4) { stationsImg.attr('src','/styles/img/map_es.jpg') } 
		else if (dtype==12) { stationsImg.attr('src','/styles/img/map_dv.jpg') } 
		else { stationsImg.attr('src','/styles/img/map.jpg') } 
		
	})
	
	dynastyImages = $("#dynasty-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 0, slideBy: 3, mouseDrag: false,
		responsive : {
			0 : { items : 4 },	
			1600 : { items : 5 },	
			1800 : { items : 5 }
		},
		onInitialized: function(event){
			ips = $("#dynasty-list").find('.owl-item').first().find('i').position();
			if(ips) { $("#dynasty-list").find('.dynasty-list-dots').css('left',ips.left-11).css('top',ips.top+2); }

		}
	});
	
	galleryIndex = 1;
	
	dynastySlider = $("#dynastySlider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false
	});		
	
	dynastySlider.on('changed.owl.carousel', function(event) {
		galleryIndex = 1;
		$('.gallery-right-in').css('top',0);
		$('.gallery-nav-counter').find('span').first().text('01');		
		if(event.item.index) $('.container').addClass('dynasty');
		else $('.container').removeClass('dynasty');		
		$('#dynasty-page').find('.dynasty-list-item').removeClass('active').eq(event.item.index).addClass('active');				
		
		ips = $("#dynasty-list").find('.owl-item').eq(event.item.index).position(); ips = ips.left;
		ips2 = $("#dynasty-list").find('.owl-item').eq(event.item.index).find('i').position(); ips2 = ips2.left;
		$("#dynasty-list").find('.dynasty-list-dots').css('left',ips+ips2-11);
		
		ajaxblock = $('#dynastySlider').find('.owl-item').eq(event.item.index).find('.gallery-ajax');
		url = $('#dynastySlider').find('.owl-item').eq(event.item.index).find('.dynasty-content-item').data('link');		
		$.ajax({
			type: "POST", 
			url: url, 						
			success: function(data) {
				ajaxblock.html(data);
				ajaxblock.find('a.fancybox').fancybox();				
				ajaxblock.find('a.fancyboxvideo').fancybox({type:'html',maxHeight: 550, maxWidth: 800});
			}
		});		
		
	})
	
	$('#dynasty-page').find('.dynasty-list-item').on('click',function(){
		$('#dynastySlider').find('.owl-dot').eq($(this).parents('.owl-item').index()).trigger('click');
	})
	
	
	galleryMargin = 30;
	
	$('body').on('click','.gallery-nav-next',function(){		
		galleryDynasty = $(this).parent().prev('.gallery-right').find('.gallery-right-in');
		galleryPrev = $(this).parent().find('.gallery-nav-prev');
		if($(this).parents('#stationsPopup')[0]) galleryMargin = 0;
		galleryDynastyCount = galleryDynasty.find('.gallery-right-item').length;
		if(galleryIndex<galleryDynastyCount) {			
			galleryDynasty.css('top',-((292+galleryMargin)*galleryIndex));	
			galleryIndex++;
			galleryPrev.removeClass('disabled');
		}
		if(galleryIndex==galleryDynastyCount) $(this).addClass('disabled');
		
		galleryCounter = $(this).parent().find('.gallery-nav-counter').find('span').first();		
		ic = galleryIndex;
		if (ic<10) { ic = "0"+ic }
		galleryCounter.text(ic)

	})
	
	$('body').on('click','.gallery-nav-prev',function(){		
		galleryDynasty = $(this).parent().prev('.gallery-right').find('.gallery-right-in');
		galleryNext = $(this).parent().find('.gallery-nav-next');
		if($(this).parents('#stationsPopup')[0]) galleryMargin = 0;
		galleryDynastyCount = galleryDynasty.find('.gallery-right-item').length;
		if(galleryIndex>1) {			
			galleryIndex--;
			galleryDynasty.css('top',-((292+galleryMargin)*(galleryIndex-1)));				
			galleryNext.removeClass('disabled');
		}
		if(galleryIndex==1) $(this).addClass('disabled');
		
		galleryCounter = $(this).parent().find('.gallery-nav-counter').find('span').first();		
		ic = galleryIndex;
		if (ic<10) { ic = "0"+ic }
		galleryCounter.text(ic);

	})	
	
	gooddeedsImages = $("#gooddeeds-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 0, slideBy: 3, mouseDrag: false,
		responsive : {
			0 : { items : 5 },	
			1600 : { items : 5 },	
			1800 : { items : 6 }
		},
		onInitialized: function(event){
			ips = $("#gooddeeds-list").find('.owl-item').first().find('i').position();
			if(ips) { $("#gooddeeds-list").find('.dynasty-list-dots').css('left',ips.left-11).css('top',ips.top+2); }
		}
	});
	
	gooddeedsSlider = $("#gooddeedsSlider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false
	});	
	
	gooddeedsSlider.on('changed.owl.carousel', function(event) {
		galleryIndex = 1;
		$('.gallery-right-in').css('top',0);
		$('.gallery-nav-counter').find('span').first().text('01');		
		if(event.item.index) $('.container').addClass('dynasty');
		else $('.container').removeClass('dynasty');		
		$('#good-deeds').find('.dynasty-list-item').removeClass('active').eq(event.item.index).addClass('active');				
		
		ips = $("#gooddeeds-list").find('.owl-item').eq(event.item.index).position(); ips = ips.left;
		ips2 = $("#gooddeeds-list").find('.owl-item').eq(event.item.index).find('i').position(); ips2 = ips2.left;
		$("#gooddeeds-list").find('.dynasty-list-dots').css('left',ips+ips2-11);
		
		ajaxblock = $('#gooddeedsSlider').find('.owl-item').eq(event.item.index).find('.gallery-ajax');
		url = $('#gooddeedsSlider').find('.owl-item').eq(event.item.index).find('.dynasty-content-item').data('link');
		$.ajax({
			type: "POST", 
			url: url, 						
			success: function(data) {
				ajaxblock.html(data);
				ajaxblock.find('a.fancybox').fancybox();
				
			}
		});
		
	})
	
	$('#good-deeds').find('.dynasty-list-item').on('click',function(){
		$('#gooddeedsSlider').find('.owl-dot').eq($(this).parents('.owl-item').index()).trigger('click');
	})	
	
	$('.top-reliz').on('click',function(){
		$('#directorSpeech').addClass('active');
		fleXenv.updateScrollBars();
	})
	
	hamburgerPopup = $('#hamburger-popup');
	
	$('.menu-hamburger-item').on('click',function(){
		hamburgerPopup.removeClass('active');
		$('.landing-menu-item').eq($(this).index()).trigger('click');
	})	
	
	
	$.ajax({
		type: "POST", 
		url: '/ajax/historybg.php', 						
		success: function(data) {
			$('#landing-page-bg').html(data);			
		}
	});		

	$('#good-deeds-bg').css('background-image','url(tmp/gooddeedsbg.jpg)');
	$('#dynasty-page-bg').css('background-image','url(tmp/dynastybg.jpg)');
	$('#historyhydro-bg').css('background-image','url(tmp/history.jpg)');
	
	historyhydroSlider = $("#historyhydroSlider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false
	});	
	
	historyhydroSlider.on('changed.owl.carousel', function(event) {
		galleryIndex = 1;
		$('.gallery-right-in').css('top',0);
		$('.gallery-nav-counter').find('span').first().text('01');		
		if(event.item.index) $('.container').addClass('dynasty');
		else $('.container').removeClass('dynasty');		
		$('#historyhydro').find('.dynasty-list-item').removeClass('active').eq(event.item.index).addClass('active');		
		
		ips = $("#historyhydro-list").find('.owl-item').eq(event.item.index).position(); ips = ips.left;
		ips2 = $("#historyhydro-list").find('.owl-item').eq(event.item.index).find('i').position(); ips2 = ips2.left;
		$("#historyhydro-list").find('.dynasty-list-dots').css('left',ips+ips2-11);
		
		ajaxblock = $('#historyhydroSlider').find('.owl-item').eq(event.item.index).find('.gallery-ajax');
		url = $('#historyhydroSlider').find('.owl-item').eq(event.item.index).find('.dynasty-content-item').data('link');
		$.ajax({
			type: "POST", 
			url: url, 						
			success: function(data) {
				ajaxblock.html(data);
				ajaxblock.find('a.fancybox').fancybox();
				
			}
		});
		
	})
	
	$('#historyhydro').find('.dynasty-list-item').on('click',function(){
		$('#historyhydroSlider').find('.owl-dot').eq($(this).parents('.owl-item').index()).trigger('click');
	})
	
	historyhydroList = $("#historyhydro-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 0, slideBy: 3, mouseDrag: false,
		responsive : {
			0 : { items : 7 },	
			1600 : { items : 7 },	
			1800 : { items : 8 }
		},
		onInitialized: function(event){
			ips = $("#historyhydro-list").find('.owl-item').first().find('i').position();
			if(ips) { $("#historyhydro-list").find('.dynasty-list-dots').css('left',ips.left-11).css('top',ips.top+2); }

		}	
			
		
	});	
	
	$('.logo').on('click',function(){
		$('.landing-menu-item').first().click();
		stationsPopup.removeClass('active');
		hamburgerPopup.removeClass('active')
	})
	
	
	$('.landing-page').on('mousewheel', function(e) {
		if($(e.target).parents('.gallery-right')[0]) {
			if( e.deltaY>0 ) $(e.target).parents('.gallery-ajax').find('.gallery-nav-prev').click();
			if( e.deltaY<0 ) $(e.target).parents('.gallery-ajax').find('.gallery-nav-next').click();
		}		
	})	

	
	$('.dynasty-list-in').on('mousewheel', function(e) {		
		if( e.deltaY<0 ) $(this).find('.owl-next').trigger('click');
		if( e.deltaY>0 ) $(this).find('.owl-prev').trigger('click');
	})	
	
	
})

