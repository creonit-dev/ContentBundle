var Creonit;
(function (Creonit) {
    var Admin;
    (function (Admin) {
        var Component;
        (function (Component) {
            var Helpers;
            (function (Helpers) {
                function content(value, _a) {
                    var _b = _a === void 0 ? ['', []] : _a, _c = _b[0], name = _c === void 0 ? '' : _c, _d = _b[1], types = _d === void 0 ? [] : _d;
                    if (!value) {
                        return 'ошибка';
                    }
                    var allowedTypes = [];
                    $.each(CreonitContentTypes, function (i, type) {
                        if (!types.length || types.indexOf(i) >= 0) {
                            allowedTypes.push($.extend({}, type, { name: i }));
                        }
                    });
                    return "\n            <div class=\"component-field-content-types " + (allowedTypes.length < 2 ? 'is-hidden' : '') + "\"> \n                " + allowedTypes.map(function (type) {
                        return "<a href=\"#\" data-component=\"" + (type.component || '') + "\" data-name=\"" + type.name + "\"><i class=\"fa fa-" + (type.icon || 'pencil-square-o') + "\"></i>" + type.title + "</a>";
                    }).join('') + "\n            </div>\n            <div class=\"component-field-content\" data-name=\"" + name + "\">\n                " + allowedTypes.map(function (type) {
                        return "\n                        <div class=\"component-field-content-type\" data-name=\"" + type.name + "\">\n                            " + (type.component ? '' : Helpers.textedit(value.text, [name + '__text', {}])) + "\n                        </div>\n                    ";
                    }).join('') + "\n            </div>\n            <input type=\"hidden\" name=\"" + name + "\" value=\"" + value.id + "\">\n        ";
                }
                Helpers.content = content;
                Helpers.registerTwigFilter('content', content);
            })(Helpers = Component.Helpers || (Component.Helpers = {}));
        })(Component = Admin.Component || (Admin.Component = {}));
    })(Admin = Creonit.Admin || (Creonit.Admin = {}));
})(Creonit || (Creonit = {}));
var Creonit;
(function (Creonit) {
    var Admin;
    (function (Admin) {
        var manager = Admin.Manager.getInstance();
        manager.on('component_render', function (data) {
            var component = data.component, $component = data.component.getNode();
            $component.find('.component-field-content').each(function () {
                var $content = $(this), $types = $content.prev('.component-field-content-types').find('a'), $contentTypes = $content.find('.component-field-content-type');
                $types.on('click', function () {
                    var $contentType = $contentTypes.filter("[data-name=" + $(this).data('name') + "]");
                    $(this).addClass('is-active').siblings('a').removeClass('is-active');
                    component.parameters[("content_type_" + $component.data('name'))] = $(this).data('name');
                    $contentTypes.hide();
                    $contentType.show();
                    if ($(this).data('component') && !$contentType.find("[" + Admin.Component.Utils.ATTR_HANDLER + "]").data('creonit-component-initialized')) {
                        $contentType.append(Admin.Component.Helpers.component($(this).data('component'), {}, {}));
                        Admin.Component.Utils.initializeComponents($contentType, component);
                    }
                });
                if (component.parameters[("content_type_" + $component.data('name'))]) {
                    $types.filter("[data-name=" + component.parameters[("content_type_" + $component.data('name'))] + "]").click();
                }
                else {
                    $types.eq(0).click();
                }
            });
        });
    })(Admin = Creonit.Admin || (Creonit.Admin = {}));
})(Creonit || (Creonit = {}));
