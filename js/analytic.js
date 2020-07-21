$(document).ready(function() {


	//Создаем скрытые поля в форме
		$("form").append("<input type='hidden' name='utm_source' class='utm_source' value='none' />");
		$("form").append("<input type='hidden' name='utm_campaign' class='utm_campaign' value='none' />");
		$("form").append("<input type='hidden' name='utm_content' class='utm_content' value='none' />");
		$("form").append("<input type='hidden' name='utm_term' class='utm_term' value='none' />");
		$("form").append("<input type='hidden' name='cid' class='cid' value='cid' />");
		$("form").append("<input type='hidden' name='roistat' class='roistat' value='none' />");

	setTimeout(setCID,10000);
	setTimeout(utm_form,10000); // это в начало дока под document.ready
	setTimeout(setRoiCookie,10000);  //Roistat ID в форму

	// $("form").submit(function() {
	//     var form = $(this).closest("form");
	//     var phone = $(form).find("[type=tel]").val();
	//     if (phone.indexOf("_") != -1 || phone == "" || phone == undefined) {return false;}       
	//     data = $(form).serialize();
	//     dataLayer.push({'event': 'formSubmit'});
	//     console.log(data);
	//     leadCollect(data);

	// });


	function setCID() {
	        if(ga!=undefined){
	        var tracker = ga.getAll()[0];
	        var cid = tracker.get('clientId');
	        $('.cid').each(function(index, el) {
	                $(el).val(cid);
	        });
	        }
	}


	setTimeout(setCID,10000);


	function leadCollect(data) {
	       
	        $.ajax({
	                type: "post",
	                url: "/js/lead-collect.php",
	                data: data,
	                success: function() {
	                	console.log('lead-collect')
	                }
	        });
	        return false;
	}


	function utm_form(){
		var utm_source = getUrlVars()["utm_source"];
		var utm_campaign = getUrlVars()["utm_campaign"];
		var utm_content = window.decodeURIComponent(getUrlVars()["utm_content"]);
		var utm_term = window.decodeURIComponent(getUrlVars()["utm_term"]);

		$('.utm_source').val(utm_source);
		$('.utm_campaign').val(utm_campaign);
		$('.utm_content').val(utm_content);
		$('.utm_term').val(utm_term);
	}

	function getUrlVars() {
	        var vars = [], hash;
	        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	                for(var i = 0; i < hashes.length; i++)
	                {
	                        hash = hashes[i].split('=');
	                        vars.push(hash[0]);
	                        vars[hash[0]] = hash[1];
	                }
	        return vars;
	}


function setRoiCookie(){
	var roistat = getCookie('roistat_visit');
	$('.roistat').val(roistat);
}


function getCookie(cName){
// разделение куков
var    cookieStr = document.cookie,                  // получаем строку куков
       cookieArray = cookieStr.split(';'),           // вспоминаем о чудесном методе split и разбиваем строку с куками на упорядоченый массив по разделителю ";"
       i, j;
 
       // удалим пробельные символы (если они, вдруг, есть) в начале и в конце у каждой куки
       for (j=0; j<cookieArray.length; j++) cookieArray[j] = cookieArray[j].replace(/(\s*)\B(\s*)/g, '');
 
var    cookieNameArray = new Array({name: '', value: new Array()});    // результирующий упорядоченный массив
                                                                       // каждый элемент будет объектом с методами name и value
                                                                       // name - имя куки, value - упорядоченный массив значений куки
 
       // обрабатываем каждую куку
       for (i=0; i<cookieArray.length; i++)
       {
           var    keyValue = cookieArray[i].split('='),               // разделяем имя и значение       
                  cookieVal = unescape(keyValue[1]).split(';');       // разделяем значения, если они заданы перечнем
 
                  // удаляем пробельные символы  (если они, вдруг, есть) у значений в начале и в конце
                  for (j=0; j<cookieVal.length; j++) cookieVal[j] = cookieVal[j].replace(/(\s*)[\B*](\s*)/g, '');
                  keyValue[0] = keyValue[0].replace(/(\s*)[\B]*(\s*)/g, '');
 
                  // вот получился такой cookie-объект
                  cookieNameArray[i] = {
                      name: keyValue[0],
                      value: cookieVal
                  };
       };
 
    var cookieNALen = cookieNameArray.length;    // размер полученного массива
 
        // выбираем нужную куку
        if (!cName) return cookieNameArray
            else 
                for (i=0; i<cookieNALen; i++) if (cookieNameArray[i].name == cName) return cookieNameArray[i].value;
     return false; 
};


});