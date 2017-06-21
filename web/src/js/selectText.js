/**
 * Created by sergey on 12.06.17.
 */


function eventSelection() {

    // Получаем выделенный текст
    var selected = (document.all) ? document.selection.createRange().text : document.getSelection();

    var text = selected.toString();

    // Передаем на вывод
    if(text.length > 0){
        console.log(text);
    }
}

// Когда контент загружен
document.addEventListener('DOMContentLoaded', function () {

    // навешиваем слушателя
    document.addEventListener('mouseup', eventSelection);
});