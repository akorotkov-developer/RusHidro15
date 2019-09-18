$(function(){	
	
	infoslider = $("#info-block-slider").owlCarousel({
		items: 1, dots: false, autoplay: true, nav:false, animateIn: 'fadeIn', animateOut: 'fadeOut', loop: true
	});
	
	infoslider.on('changed.owl.carousel', function(event) {		
		eind = $("#info-block-slider").find('.owl-item').eq(event.item.index).find('.info-block-slider_item').data('image');
		$('#index-illustration').find('.illustration').removeClass('active');
		$('#'+eind).addClass('active');		
	})	
	
	dateslider = $("#date-block-slider").owlCarousel({
		items: 1, dots: false, autoplay: true, nav:false, animateIn: 'fadeIn', animateOut: 'fadeOut', loop: true, mouseDrag: false, autoplayTimeout: 6000
	});
	
	/*====history===*/
	historySlider = $("#history-slider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false 
	});
	
	hdw_trigger = 1;
	
	sc = $('.history-dots-over-scroll');			
	
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
		
		$('.history-dots-nav-next').addClass('active');
		$('.history-dots-nav-prev').addClass('active');
		nn = $('.history-dot.active').next('.history-dot');
		np = $('.history-dot.active').prev('.history-dot');
		cpi = 50;
		if(!nn[0]) { $('.history-dots-nav-next').removeClass('active'); cpi = 100 }
		if(!np[0]) { $('.history-dots-nav-prev').removeClass('active'); cpi = 0 }
		
		cp = dot.position(); cp = cp.left-cpi;
		sc.css('left',-cp);		
		
	})
	
	$('.history-dots-nav-next').on('click',function(){
		if($(this).hasClass('active')) {			
			$('.history-dots-nav-prev').addClass('active');			
			np = $('.history-dot.active').next('.history-dot');
			np2 = $('.history-dot.active').next('.history-dot').next('.history-dot');			
			if(np[0]) {
				np.click();
				npl =  np.position(); npl = npl.left-50;
				if(np2[0]) sc.css('left',-npl);
			}
			if(!np2[0]) $('.history-dots-nav-next').removeClass('active');			
		}
	})
	
	$('.history-dots-nav-prev').on('click',function(){
		if($(this).hasClass('active')) {			
			$('.history-dots-nav-next').addClass('active');			
			nn = $('.history-dot.active').prev('.history-dot');
			nn2 = $('.history-dot.active').prev('.history-dot').prev('.history-dot');			
			if(nn[0]) {
				nn.click();
				nnl =  nn.position(); nnl = nnl.left;
				nnl2 = 0;
				if(nn2[0]) { nnl2 = nn2.position(); nnl2 = nnl2.left; }
				if(nnl2) sc.css('left', -nnl2); else sc.css('left', -nnl)
			}
			if(!nn2[0]) $('.history-dots-nav-prev').removeClass('active');			
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
	
	$('.status-select-title').on('click',function(){
		$('#status-select').slideToggle();
		$(this).toggleClass('active')
	})
	
	stationsSlider = $("#stations-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 20, slideBy: 3,
		responsive : {
			0 : { items : 2 },	
			400 : { items : 3 },	
			500 : { items : 4 },	
			1600 : { items : 8 },	
			1800 : { items : 9 }
		}
	});
	
	stationsMap = $('#stations-map');
	
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
	
	$('.stations-map-descr').find('a').on('click',function(e){
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
		stationsPopup.removeClass('active'); $('body').removeClass('noscroll')
	})
	
	$(document).on('keydown', function(e){ 	
		if (e.keyCode == 27) { $('body').find('.stations-popup').removeClass('active')}; 
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
		$('#status-select').slideUp();
	})
	
	dynastyImages = $("#dynasty-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 20, slideBy: 3,
		responsive : {
			0 : { items : 2 },	
			400 : { items : 2 },	
			500 : { items : 2 },	
			1600 : { items : 8 },	
			1800 : { items : 9 }
		},
		onInitialized:function(e){
			eps = e.page.size;			
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
		if(event.item.index) $('#dynasty-page').addClass('dynasty');
		else $('#dynasty-page').removeClass('dynasty');		
		$('#dynasty-page').find('.dynasty-list-item').removeClass('active').eq(event.item.index).addClass('active');
		ei = Math.floor(event.page.index / eps);		
		dynastyImages.trigger('to.owl.carousel', ei);		
		
	})
	
	$('#dynasty-page').find('.dynasty-list-item').on('click',function(){
		$('#dynastySlider').find('.owl-dot').eq($(this).parents('.owl-item').index()).trigger('click');
	})
	
	
	galleryMargin = 20;
	
	$('body').on('click','.gallery-nav-next',function(){		
		galleryDynasty = $(this).parent().prev('.gallery-right').find('.gallery-right-in');
		galleryPrev = $(this).parent().find('.gallery-nav-prev');
		if($(this).parents('#stationsPopup')[0]) galleryMargin = 0;
		galleryDynastyCount = galleryDynasty.find('.gallery-right-item').length;
		if(galleryIndex<galleryDynastyCount) {			
			galleryDynasty.css('left',-((210+galleryMargin)*galleryIndex));	
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
			galleryDynasty.css('left',-((210+galleryMargin)*(galleryIndex-1)));				
			galleryNext.removeClass('disabled');
		}
		if(galleryIndex==1) $(this).addClass('disabled');
		
		galleryCounter = $(this).parent().find('.gallery-nav-counter').find('span').first();		
		ic = galleryIndex;
		if (ic<10) { ic = "0"+ic }
		galleryCounter.text(ic);

	})	
	
	gooddeedsImages = $("#gooddeeds-list").owlCarousel({
		dots: true, autoplay: false, nav:true, margin: 20, slideBy: 3,
		responsive : {
			0 : { items : 2 },	
			400 : { items : 2 },	
			500 : { items : 3 },	
			1600 : { items : 8 },	
			1800 : { items : 9 }
		},
		onInitialized:function(e){
			eps2 = e.page.size;			
		}
	});
	
	gooddeedsSlider = $("#gooddeedsSlider").owlCarousel({
		items: 1, dots: true, autoplay: false, nav:false
	});	
	
	gooddeedsSlider.on('changed.owl.carousel', function(event) {
		galleryIndex = 1;
		$('.gallery-right-in').css('top',0);
		$('.gallery-nav-counter').find('span').first().text('01');		
		if(event.item.index) $('#good-deeds').addClass('dynasty');
		else $('#good-deeds').removeClass('dynasty');		
		$('#good-deeds').find('.dynasty-list-item').removeClass('active').eq(event.item.index).addClass('active');				
		ei2 = Math.floor(event.page.index / eps2);		
		gooddeedsImages.trigger('to.owl.carousel', ei2);		
	})
	
	$('#good-deeds').find('.dynasty-list-item').on('click',function(){
		$('#gooddeedsSlider').find('.owl-dot').eq($(this).parents('.owl-item').index()).trigger('click');
	})	
	
	$('.top-reliz').on('click',function(){
		$('#directorSpeech').addClass('active');
		$('body').addClass('noscroll');
	})
	
	hamburgerPopup = $('#hamburger-popup');
	
	$('.menu-hamburger-item').on('click',function(){
		hamburgerPopup.removeClass('active');
		$('body').removeClass('noscroll');		
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
	
	addpopupGallery = $("#addpopupGallery").owlCarousel({
		autoWidth: true, margin: 20, dots: false, autoplay: false, nav:true 
	});	
	
	
	$('.dynasty-content').find('.show-more').on('click',function(e){
		e.preventDefault();
		url = $(this).find('a').attr('href')+'?mobile=1';
		ajaxblock = $('#addinfoPopup');
		console.log(url)
		$.ajax({
			type: "POST",
			url: url,
			success: function(data) {
				ajaxblock.addClass('active');
				$('body').addClass('noscroll');
				ajaxblock.html(data);
				addpopupGallery = $("#addpopupGallery").owlCarousel({
					autoWidth: true, margin: 20, dots: false, autoplay: false, nav:true 
				});
				addpopupGallery.on('changed.owl.carousel', function(event) {
					galleryCounter = $(this).parent().find('.addpopup-gallery-counter').find('span').first();		
					ic = event.item.index+1;
					if (ic<10) { ic = "0"+ic }
					galleryCounter.text(ic);
				})
				ajaxblock.find('a.fancybox').fancybox();				
				ajaxblock.find('a.fancyboxvideo').fancybox({type:'html',maxHeight: 320, maxWidth: 800});
			}
		});
		
	})
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
})

