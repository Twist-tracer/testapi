define(['jquery', 'lib/components/base/modal'], function($,  Modal){
    var CustomWidget = function () {
    	var self = this;


		this.callbacks = {
			render: function(){
				console.log('render');
				return true;
			},
			init: function(){
				console.log('init');
				return true;
			},
			bind_actions: function(){
				return true;
			},
			settings: function(){
				return true;
			},
			onSave: function(){
				alert('click');
				return true;
			},
			destroy: function(){
				
			},
			contacts: {
					//select contacts in list and clicked on widget name
					selected: function(){
						console.log('contacts');
					}
				},
			leads: {
					//select leads in list and clicked on widget name
					selected: function(){
						var w_code = self.get_settings().widget_code;

						var data =
							'<h1 class="modal-body__title">Экспорт сделок</h1>' +
							'<div class="export-settings modal-body__export-settings">' +
								'<h2 class="export-settings__title">Настройки экспорта</h2>' +
								'<form class="export-settings__form" action="#" method="post">' +
									'<div class="form-checklist export-settings__form-checklist">' +
										'<div class="form-checklist__title">Выберете поля для экспорта:</div>' +
										'<label class="form-checklist__item">' +
											'<input type="checkbox" name="field[]">' +
											'Название' +
										'</label>' +
									'</div>' +
									'<button type="submit" class="button-input">' +
										'<span class="button-input-inner ">' +
											'<span class="button-input-inner__text">Экспорт</span>' +
										'</span>' +
									'</button>' +
								'</form>' +
							'</div>' +
							'<link type="text/css" rel="stylesheet" href="/widgets/'+w_code+'/style.css" >'

						var modal = new Modal({
							class_name: 'modal-window',
							init: function ($modal_body) {
								var $this = $(this);
								$modal_body
									.trigger('modal:loaded') //запускает отображение модального окна
									.html(data)
									.trigger('modal:centrify')  //настраивает модальное окно
									.append('<span class="modal-body__close"><span class="icon icon-modal-close"></span></span>');
							},
							destroy: function () {

							}
						});
					}
				},
			tasks: {
					//select taks in list and clicked on widget name
					selected: function(){
						console.log('tasks');
					}
				}
		};
		return this;
    };

return CustomWidget;
});