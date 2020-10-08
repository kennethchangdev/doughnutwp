if (typeof(licode) === 'undefined') {
    var licode = {};
}

/**
 * Контроллер ("синглтон"), отвечает за отображение прелоадера
 * @type {licode.preloader}
 */
licode.preloader = new (function() {
    /**
     * Элемент, отрисовывающий прелоадер
     * @type string
     */
    this.element = '<img src="data:image/gif;base64,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">';
    
    /**
     * Отрисовывает прелоадер поверх блочного элемента
     * @param {object} block Элемент, для которого отрисовывается прелоадер
     * @return {undefined}
     */
    this.on = function(block) {
        var obj = jQuery(block);
        var div = jQuery('<div class="async-preloader"></div>');
        var elementPreload = jQuery(this.element);
        div.append(elementPreload);
        div.css({
            'position' : 'absolute',
            'background-color' : 'rgba(255,255,255,0.8)',
            'z-index' : '10',
            'text-align' : 'center'
        });
        div.width(obj.width());
        div.height(obj.height());
        div.position().left = obj.position().left;
        div.position().top = obj.position().top;
        
        obj.prepend(div);
        elementPreload.css({'margin-top': div.height() / 2 - 25 + 'px'});        
    };
    
    /**
     * Удаляет отрисованный прелоадер в блочном элементе
     * @param {object} block Элемент, для которого покрывается прелоадер
     * @returns {undefined}
     */
    this.off = function(block) {
        jQuery(block).find('.async-preloader').remove();
    };
});
