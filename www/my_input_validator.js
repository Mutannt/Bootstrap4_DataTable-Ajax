//Объектный подход	

//Наша собственная библиотека классов для валидации форм,
//стилизованных библиотекой bootstrap

//------~[Объявление класса InputForm]~------	
//Класс InputForm, представляет всю форму ввода полностью
//selector - селектор той формы, с которой следует связать класс
function InputForm(selector) {
	//Свойство me, позволяющее обратиться к 
	//jQuery-обёртке формы, представляемой объектом
	this.me = $(selector);
	
	//Свойство is_valid, определяющее, является ли форма корректно заполненной
	this.is_valid=true;
	
	//Callback-функция (функция обратного вызова, событие, пользовательская функция)
	//вызываемая при попытке отправки формы
	this.beforesubmit=function() {};
	
	//Обработчик нажатия на кнопку submit (попытки отправки формы)
	var input_form = this;//так как в обработчике будет свой this, запомним текущий this в переменной input_form
	this.me.find('input[type="submit"]').click(function() {	
		input_form.is_valid=true;
		//вызов пользовательской функции для валидации
		input_form.beforesubmit();
		//Предотвращение дефолтной реакции на нажатие
		//кнопки с типом Submit в случае некорректно 
		//заполненной формы
		return input_form.is_valid;
	});
}

//------~[Объявление класса InputField]~-------	
//класс InputField	 представляет какой-то один отдельный элемент управления
//selector - селектор того элемента управления, с которым следует связать класс,
//parent_form - указатель на экземпляр объекта InputForm, ассоциированного 
//с формой, содержащей данный элемент управления
function InputField(selector,parent_form) {//эта функция становится конструктором
	
	//Свойство field, содержащее jQuery-обёртку для элемента, выделенного селектором
	this.field = $(selector);
	
	//Метод v, позволяет получить быстрый доступ к значению, введённому в поле
	this.v=function() {
		return this.field.val().trim();
	}
	
	//Метод validate, выполняющий собственно валидацию
	this.validate = function(is_valid) {
		if(is_valid) {
			//Если ошибка
			$(selector).removeClass("is-valid");
			$(selector).addClass("is-invalid");
			parent_form.is_valid=false;
		} else {
			//Если успех
			$(selector).removeClass("is-invalid");
			$(selector).addClass("is-valid");
		}
	}			
}		