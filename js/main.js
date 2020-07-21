$(function () {
	// Menu opener hamburger
    $('.bar').click(function () {
        $('.menu-collapse').toggleClass('d-none').css('order', '1');
        $('.menu').toggleClass('menu-opened');
    });
	
	// Анимация ваша персональная скидка

	setInterval(function () {
		$('.offer__discount').toggleClass('animated flash');
	}, 4000);

	// Анимация картинки при выборе товара
	
	$('.variable').addClass('animated bounceInUp');
	setTimeout(function () {
		$('.variable').removeClass('bounceInUp');
		$('.variable').addClass('shake');
	}, 1000);
	
	$("input[name='a']").click(function() {
		$('.variable').removeClass('shake');
		$('input').removeAttr('checked');
		switch($(this).val()||$(this).attr('value')) {
			case 'Строганный брус': 
				$('input[value="Строганный брус"]').attr('checked','');
			    $('.variable__img').attr("src","img/planed-beam.png");
			    $('.variable__price').html("От <span>11000р</span> за м3");
			    break;
			
			case 'Строганная доска': 
				$('input[value="Строганная доска"]').attr('checked','');
			    $('.variable__img').attr("src","img/planed-board.png");
			    $('.variable__price').html("От <span>11500р</span> за м3");
			    break;
			
			case 'Имитация бруса': 
				$('input[value="Имитация бруса"]').attr('checked','');
			    $('.variable__img').attr("src","img/imitation-timber.png");
			    $('.variable__price').html("От <span>260р</span> за м2");
			    break;
			
			case 'Европол': 
				$('input[value="Европол"]').attr('checked','');
			    $('.variable__img').attr("src","img/euro-floor.png");
			    $('.variable__price').html("От <span>350р</span> за м2");
			    break;
			
			case 'Брусок': 
				$('input[value="Брусок"]').attr('checked','');
			    $('.variable__img').attr("src","img/bar.png");
			    $('.variable__price').html("От <span>25р</span> за шт");
			    break;
			
			case 'Евровагонка': 
				$('input[value="Евровагонка"]').attr('checked','');
			    $('.variable__img').attr("src","img/floor-board.png");
			    $('.variable__price').html("От <span>180р</span> за м2");
			    break;
			
			case 'Обрезной брус': 
				$('input[value="Обрезной брус"]').attr('checked','');
			    $('.variable__img').attr("src","img/cut-bar.png");
			    $('.variable__price').html("От <span>7000р</span> за м3");
			    break;
			
			case 'Обрезная доска': 
				$('input[value="Обрезная доска"]').attr('checked','');
			    $('.variable__img').attr("src","img/edged-board.png");
			    $('.variable__price').html("От <span>6000р</span> за м3");
			    break;
			
			case 'Половая доска': 
				$('input[value="Половая доска"]').attr('checked','');
			    $('.variable__img').attr("src","img/euro-lining.png");
			    $('.variable__price').html("От <span>350р</span> за м2");
			    break;
			
			case 'Блок-Хаус': 
				$('input[value="Блок-Хаус"]').attr('checked','');
			    $('.variable__img').attr("src","img/block-house.png");
			    $('.variable__price').html("От <span>350р</span> за м2");
			    break;
			
			case 'Брус сух.строг': 
				$('input[value="Брус сух.строг"]').attr('checked','');
			    $('.variable__img').attr("src","img/bar-dry-strict.png");
			    $('.variable__price').html("От <span>11500р</span> за м3");
			    break;
			
			case 'Доска-четверть': 
				$('input[value="Доска-четверть"]').attr('checked','');
			    $('.variable__img').attr("src","img/board-quarter.png");
			    $('.variable__price').html("От <span>270р</span> за м2");
			    break;

                case 'Брус сух. обрез.': 
                $('input[value="Брус сух. обрез."]').attr('checked','');
                $('.variable__img').attr("src","img/briquet.png");
                $('.variable__price').html("От <span>11000р</span> за м3");
                break;

                case 'Доска сух. обрез.': 
                $('input[value="Пеллеты"]').attr('checked','');
                $('.variable__img').attr("src","img/edged_board.png");
                $('.variable__price').html("От <span>11500р</span> за м3");
                break;

                case 'Клеёный брус': 
                $('input[value="Клеёный брус"]').attr('checked','');
                $('.variable__img').attr("src","img/kleenyy_brus.png");
                $('.variable__price').html("От <span>34000р</span> за м3");
                break;
		}
		$('.variable').addClass('bounceInUp');
		setTimeout(function () {
			$('.variable').removeClass('bounceInUp');
			$('.variable').addClass('shake');
		}, 1000);
	});	

	// Модальное окно arcticModal
	$('.popup').arcticmodal('setDefault', {
		overlay: {
			css: {
				backgroundColor: '#eae7e7'
			}
		}    
	});
	$('.block-tree__link').click(function(e) {
		e.preventDefault();
		$('#popup').arcticmodal()
	});

	// Слайдер

	$('.slider-img').slick({
		   	infinite: false,
        	autoplay: true,
	        prevArrow: '<button type="button" class="slick-prev"><i class="fa fa-angle-left"></i></button>',
	        nextArrow: '<button type="button" class="slick-next"><i class="fa fa-angle-right"></i></button>'
	});

	// плавное перемещение страницы к нужному блоку

	$("a.go").click(function (e) {
	    e.preventDefault();
	    elementClick = $(this).attr("href");
	    destination = $(elementClick).offset().top;
	    $("body,html").animate({scrollTop: destination }, 900);
	});

	//Валидация и отправка формы

	$('[data-submit]').on('click', function(e) {
        e.preventDefault();
        $(this).parent('form').submit();
    });

    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please check your input."
    );

    // Функция валидации и вывода сообщений
    function valEl(el) {

        el.validate({
            rules: {
                tel: {
                    required: true,
                    regex: '^([\+]+)*[0-9\x20\x28\x29\-]{5,20}$'
                },
                name: {
                    required: true,
                    regex: '^[_a-zA-Z0-9а-яА-ЯёЁ ]+$'
                },
                // email: {
                //     required: true,
                //     email: true
                // },
                width: {
                	required: true,
                	regex: '[1-9]'
                },
                thickness: {
                	required: true,
                	regex: '[1-9]' 
                },
                count: {
                	required: true,
                	regex: '[1-9]'
                }
            },
      

            // Начинаем проверку id="" формы
            submitHandler: function(form) {
                var $form = $(form);
                var $formId = $(form).attr('id');
                switch ($formId) {
                    // Если у формы id="popupResult" - делаем:
                    case 'popupResult': 
                        $.ajax({
                                type: 'POST',
                                url: $form.attr('action'),
                                data: $form.serialize(),
                            })
                            .always(function(response) {
                                setTimeout(function() {
                                	leadCollect($form.serialize());
                                    $('.form__overlay').fadeIn();
                                    $form.trigger('reset');
                                    $('.form__param-item,.form__email, .form__tel, .form-call__name, .form-call__phone').removeClass('valid');
                                    //строки для остлеживания целей в Я.Метрике и Google Analytics
                                }, 1100);
                                $('.form__overlay').on('click', function(e) {
                                    $(this).fadeOut();
                                });
                            });
                        break;
                    
                    case 'ajaxform': 
                        $.ajax({
                                type: 'POST',
                                url: $form.attr('action'),
                                data: $form.serialize(),
                            })
                            .always(function(response) {
                                setTimeout(function() {
                                	leadCollect($form.serialize());
                                    $('.form__overlay2').fadeIn();
                                    $form.trigger('reset');
                                    //строки для остлеживания целей в Я.Метрике и Google Analytics
                                }, 1100);
                                $('.form__overlay2').on('click', function(e) {
                                    $(this).fadeOut();
                                });
                            });
                        break;
                    
                    case 'popupForm': 
                        $.ajax({
                                type: 'POST',
                                url: $form.attr('action'),
                                data: $form.serialize(),
                            })
                            .always(function(response) {
                                setTimeout(function() {
                                	leadCollect($form.serialize());
                                    $('.form__overlay3').fadeIn();
                                    $form.trigger('reset');
                                    //строки для остлеживания целей в Я.Метрике и Google Analytics
                                }, 1100);
                                $('.form__overlay3').on('click', function(e) {
                                    $(this).fadeOut();
                                });
                            });
                        break;
                }
                return false;
            }
        });
    }

    // Запускаем механизм валидации форм, если у них есть класс .js-form
    $('.js-form').each(function() {
        valEl($(this));
    });

    // Маска поля ввода формы
    $('.form__param-item:nth-child(1),.form__param-item:nth-child(2)').mask('9?99 мм');
    $('.form__param-item:nth-child(3)').mask('9?99 шт');
    $('.form__tel, .form-call__phone').mask('+7 (999) 999-99-99');

	function leadCollect(data) {
        dataLayer.push({'event': 'formSubmit'});
        $.ajax({
                type: "post",
                url: "/js/lead-collect.php",
                data: data,
                success: function() {
                	console.log('lead-collect-ok')
                }
        });
        return false;
	}

});	
