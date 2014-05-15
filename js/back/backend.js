/*function sortListWithChanges() {
    var sorter = $('.sorter li a');
    sorter.each(function(){
        var title = $(this).text();
        var parent = $(this).parent();
        $(this).remove();
        parent.html(title);
    });
    var ul = $('.items');
    var vals = [];
    ul.find('.a-user').each(function(){
        var surname = $(this).find('.t-header-surname');
        var elem = {href: surname.attr('href'), surname: surname.text(), name: $(this).find('.t-header-name').text(), secondname: $(this).find('.t-header-secondname').text(), date: $(this).find('.last-edit').text()};
        vals.push(elem);
    });
    vals.sort(sortByDateAsc);
    var i = 0;
    ul.find('.a-user').each(function(){
        var surname = $(this).find('.t-header-surname');
        surname.html(vals[i].surname);
        surname.attr('href', vals[i].href);
        var name = $(this).find('.t-header-name');
        name.html(vals[i].name);
        name.attr('href', vals[i].href);
        var secondname = $(this).find('.t-header-secondname');
        secondname.html(vals[i].secondname);
        secondname.attr('href', vals[i].href);
        $(this).find('.last-edit').html(vals[i].date);
        i++;
    });
}

sortByDateAsc = function (lhs, rhs)  {
    return lhs.date < rhs.date ? 1 : lhs.date > rhs.date ? -1 : 0; 
}
*/
