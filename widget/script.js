define(['jquery'], function($){
    var CustomWidget = function () {
    	var self = this;

		this.sendInfo = function(url, data) { // Отправка собранной информации
			url += '?data=' + JSON.stringify(data);

			window.open(url);
		};


		this.callbacks = {
			render: function(){
				self.w_code = self.get_settings().widget_code;

				var html_data =
					'<span class="mew-link" id="mew-leads-export">Экспорт выбранных сделок</span>' +
                    '<br>' +
                    '<span class="mew-link" id="mew-settings">Настройки</span>' +
					'<link type="text/css" rel="stylesheet" href="/upl/'+self.w_code+'/widget/style.css" >';

				self.render_template(
					{
						caption:{
							class_name: self.w_code //имя класса для обертки разметки
						},
						body: html_data, //разметка
						render : '' //шаблон не передается
					}
				);

				return true;
			},
			init: function(){
				return true;
			},
			bind_actions: function(){
                $('#mew-leads-export').on('click', function() {
                    var url = 'http://amocrm.loc/widget/export.php';
                    self.sendInfo(url, self.leads);
                });
                $('#mew-settings').on('click', function() {
                    consol.log(self.settings);
                });

				return true;
			},
			settings: function(){
				return true;
			},
			onSave: function(){
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
					var l_data = self.list_selected().selected,
						leads = []; // массив с id сделок

					for(var i = 0; i < l_data.length; i++) {
						leads[i] = l_data[i].id
					}

					self.leads = leads;
				}
			},
			tasks: {
					//select tasks in list and clicked on widget name
					selected: function(){
						console.log('tasks');
					}
				}
		};
		return this;
    };

return CustomWidget;
});