/**
 * Created by sergey on 12.06.17.
 */

var TranslateModule = (function () {

    var wrapperId = "window-wrapper";

    // Объект врапера
    var wrapper;

    var currentWord = '';

    // Координаты текущего эллемента
    var Coords = {};

    /**
     * Шаблоны html разметки
     *
     * @type {{main: string, card: string, cardItem: string}}
     */
    var settings = {

        // Основное тело карточки
        main: '<div class="container">' +
                '<img id="card-img" src="{preview_url}"/><br>' +
                '<span class="card-words">{card}</span>' +
              '</div>',

        // Заголовок карточки
        card: '<code>{current}</code><br/><ul class="words">{items}</ul>',

        // Пречисление вариантов
        cardItem: "<li data-src='{preview_image_url}'>{item}</li>"
    };

    // Полифилл создаст метод trim(), если он ещё не реализуется браузером.
    if (!String.prototype.trim) {
        (function() {
            // Вырезаем BOM и неразрывный пробел
            String.prototype.trim = function() {
                return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
            };
        })();
    }

    /**
     * Формируем правильный XHR объект
     *
     * @returns {*}
     */
    var getXHR = function() {
        try{return new XMLHttpRequest();} catch(e){}
        try{return new ActiveXObject("Msxml2.XMLHTTP.6.0");} catch(e){}
        try{return new ActiveXObject("Msxml2.XMLHTTP.3.0");} catch(e){}
        try{return new ActiveXObject("Microsoft.XMLHttp");} catch(e){}

        console.err("Could not find XMLHttpRequest");
    };

    /**
     * Создание запроса
     *
     * @param uri
     * @param method
     * @param data
     */
    var makeRequest = function(uri, method, data) {

        // Определение типа
        method = method || "GET";

        // создание актуального объекта XMLHttpRequest
        var xhr=getXHR();

        xhr.open(method,uri,true);

        /**
         * Выполняем запрос
         */
        xhr.onreadystatechange = function(){

            if(xhr.readyState === XMLHttpRequest.DONE) {

                if(xhr.status===200 || xhr.status===304){

                    // Если Json
                    if (xhr.getResponseHeader("Content-Type") === "application/json") {


                        var result = JSON.parse(xhr.responseText);

                        genLayout(currentWord, Coords, result)

                    } else {

                        return xhr.responseText;
                    }
                }

            // } else {
                // console.log("Response recieved with status "+xhr.status);
            }
        };

        if(method === "POST"){
            xhr.setRequestHeader("Content-Type","application/json");
            xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");

            xhr.setRequestHeader("Access-Control-Allow-Origin","http://example.com/");
            xhr.setRequestHeader("Accept","application/json");
            xhr.send(JSON.stringify(data));
        }

        if(method === "GET"){
            xhr.send();
        }

    };

    /**
     * Генерируем ссылку на api
     *
     * @param word
     * @returns {string}
     */
    var getUrl = function(word){

        var host = "http://dictionary.skyeng.ru/";
        var api = host + "api/v2/search-word-translation";
        var needle = api + "?text=" + word;

        return needle;
    };


    /**
     *
     * @param data
     * @returns {*}
     */
    var getFirst = function (data) {
        if(data.hasOwnProperty('meanings')){
            var meanings = data.meanings;

            for(var key in meanings){

                if(meanings.hasOwnProperty(key)){
                    return meanings[key];
                }
            }
        }

        return null;
    };


    /**
     * Генерирует popup модалку
     *
     * @param word
     * @param coords
     */
    var genLayout = function(word, coords, data) {


        var words = getWords(word, data);

        // Генерируем список слов
        var card = genCardList(words);

        var fisrt = getFirst(words);

        var wrp = document.getElementById(wrapperId);

        wrp.innerHTML = templateParser(settings.main, {
            coord_x: Coords.x,
            coord_y: Coords.y,
            preview_url: fisrt.preview_image_url,
            card: card
        });

    };

    /**
     * Генерирование обёртки списка слов
     *
     * @param data
     * @returns {string}
     */
    var genCardList = function (data) {

        var current = data.hasOwnProperty("text")? data.text : "n/a" ;

        var items = genMeanings(data);

        return templateParser(
            settings.card, {
                current: current,
                items: items
            }
        );
    };

    /**
     * Генерирует список слов
     *
     * @param words
     * @returns {string}
     */
    var genMeanings = function (words) {

        var result = '';

        if(words.hasOwnProperty('meanings')){

            var meanings = words.meanings;

            for(var i in meanings){

                result += templateParser(settings.cardItem, {
                    preview_image_url: meanings[i].preview_image_url,
                    item: meanings[i].translation
                });
            }
        }

        return result;
    };

    /**
     * Поиск объекта переводов
     *
     * @param needle
     * @param data
     * @returns {*}
     */
    var getWords = function (needle, data) {

        var count = 0;

        // Создаём динамический regexp
        const regex = new RegExp('^' + needle + '$');

        // Пробежимся по массиву
        for(var key in data){

            var item = data[key];

            if(item.hasOwnProperty('text')){ // проверяем свойство

                // Проверяем на соответсвие
                if(item.text.match(regex)) return item;

                count++;
            }
        }

        if(count == 0)
            return getWords(needle.toLowerCase(), data);
    };

    /**
     * Получаем кординаты
     *
     * @param event
     * @returns {{x, y}}
     */
    var saveCoords = function(event) {
        return {
            x: event.pageX,
            y: event.pageY
        };
    };

    /**
     * Простоейший шаблонизатор
     *
     * @param template
     * @param data
     */
    function templateParser(template, data) {

        return template.replace(/\{(\w*)\}/g, function (m, key) {
            return data.hasOwnProperty(key) ? data[key] : m;
        });
    }

    /**
     * Публичные функции
     */
    return {

        /**
         * Слушатель события
         *
         * @param event object
         */
        onMouseUp : function (event) {

            Coords = saveCoords(event);

            var selected = false;

            if (window.getSelection)
                selected = window.getSelection();

            if (document.getSelection)
                selected = document.getSelection();

            if (document.selection)
                selected = document.selection.createRange().text;

            // Проверем на объект
            if(selected){

                // Переводим в текст
                var word = selected.toString().trim();

                // Передаем на вывод
                if(word.length > 0){

                    currentWord = word;

                    //  Выполняем запрос
                    makeRequest(getUrl(word), "GET");
                }
            }
        },

        /**
         * Инициализация карточка
         */
        init: function() {

            var wrapper = document.createElement('div');

            wrapper.id = wrapperId;

            wrapper.className = "wrapper-modal";
            wrapper.innerHTML = templateParser(settings.main, {
                preview_url: "",
                card: ""
            });

            // Вставляем в объект
            document.body.insertBefore(wrapper, document.body.firstChild);

            // навешиваем слушателя
            document.addEventListener('mouseup', TranslateModule.onMouseUp);
        }
    };

}());


// Когда контент загружен
document.addEventListener('DOMContentLoaded', function () {

    // навешиваем слушателя
    // document.addEventListener('mouseup', TranslateModule.onMouseUp);

    // Создаём начальный объект
    TranslateModule.init();

});