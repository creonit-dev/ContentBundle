declare var CreonitContentTypes:any;
declare var $:any;

module Creonit.Admin.Component.Helpers {

    export function content(value:any, [name = '', types = []] = ['', []]){
        if(!value){
            return 'ошибка';
        }
        
        var allowedTypes = [];

        $.each(CreonitContentTypes, function (i, type) {
            if(!types.length || types.indexOf(i) >= 0){
                allowedTypes.push($.extend({}, type, {name: i}));
            }
        });

        return `
            <div class="component-field-content-types ${allowedTypes.length < 2 ? 'is-hidden' : ''}"> 
                ${allowedTypes.map(function(type){
                    return `<a href="#" data-component="${type.component || ''}" data-id="${value.id}" data-name="${type.name}"><i class="fa fa-${type.icon || 'pencil-square-o'}"></i>${type.title}</a>`;
                }).join('')}
            </div>
            <div class="component-field-content" data-name="${name}">
                ${allowedTypes.map(function(type){
                    return `
                        <div class="component-field-content-type" data-name="${type.name}">
                            ${type.component ? '' : Helpers.textedit(value.text, [name + '__text', {}])}
                        </div>
                    `;
                }).join('')}
            </div>
            <input type="hidden" name="${name}" value="${value.id}">
        `;
    }

    Helpers.registerTwigFilter('content', content);

}

module Creonit.Admin{

    var manager = Admin.Manager.getInstance();

    manager.on('component_render', function (data:any) {
        var component = data.component,
            $component = data.component.getNode();


        $component.find('.component-field-content').each(function(){
            var $content = $(this),
                $types = $content.prev('.component-field-content-types').find('a'),
                $contentTypes = $content.find('.component-field-content-type');

            $types.on('click', function(){
                var $contentType = $contentTypes.filter(`[data-name=${$(this).data('name')}]`);

                $(this).addClass('is-active').siblings('a').removeClass('is-active');

                component.parameters[`content_type_${$component.data('name')}`] = $(this).data('name');

                $contentTypes.hide();
                $contentType.show();

                if($(this).data('component') && !$contentType.find(`[${Admin.Component.Utils.ATTR_HANDLER}]`).data('creonit-component-initialized')){
                    $contentType.append(Admin.Component.Helpers.component($(this).data('component'), {content: $(this).data('id')}, {}));
                    Admin.Component.Utils.initializeComponents($contentType, component);
                }
            });

            if(component.parameters[`content_type_${$component.data('name')}`]){
                $types.filter(`[data-name=${component.parameters[`content_type_${$component.data('name')}`]}]`).click();
            }else{
                $types.eq(0).click()

            }
        });





    });

}