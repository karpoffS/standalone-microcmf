var CORE = (function () {
    return {

        /**
         * Размеры документа
         *
         * @param {boolean} docHeight
         * @returns {object}
         */
        windowSize : function (docHeight) {
            return {
                h : $((docHeight !== undefined ? document : 'body')).height(),
                w : $('body').width()
            };
        },

        /**
         * Скролл к указанному месту
         *
         * @param {string} destination
         */
        scrollToPoint : function (destination, duration) {
            $('body, html').animate({scrollTop : destination}, (duration || 1000));
        },

        /**
         * Скролл к элементу
         *
         * @param element
         * @param duration
         */
        scrollToElement : function (element, correction, duration) {
            var offsetElement = $(element).offset();
            CORE.scrollToPoint(offsetElement.top+(correction || 0), duration);
        },

        /**
         * Перейти по адресу
         *
         * @param {string}  url
         * @param {boolean} new_tab
         */
        goTo : function (url, new_tab) {
            if (new_tab !== undefined) {
                window.open(url);
            } else {
                window.location.href = url;
            }
        },

        /**
         * Перезагрузить страницу
         */
        reloadPage : function () {
            window.location.reload();
        },

        /**
         * Установить хеш
         *
         * @param {string} hashValue
         */
        setHash : function (hashValue) {
            window.location.hash = hashValue;
        },

        /**
         * Получить хеш
         */
        getHash : function () {
            return window.location.hash;
        },

		AJAXRequest : function(className, methodName, data, success, error){
		    $.ajax({
		        type: 'POST',
		        dataType: 'json', //json|html
		        url: '/api',
		        data: {className : className, methodName : methodName, data : data},
		        success: success,
		        error: error
		    });   
		},

        templateParser : function (template, data) {
            return template.replace(/\{(\w*)\}/g, function (m, key) {
                return data.hasOwnProperty(key) ? data[key] : m;
            });
        }
    }
}());
