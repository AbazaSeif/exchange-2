/*
 *  Constructor for ajax loading content in the specified container.
 * 
 *  Конструктор для ajax загрузки контента в определенный контейнер.
 */
function AjaxContentLoader() {
    
    var _self = this;
   
    /*  
     *  Initsaliziruet ajax loading by clicking on the link, where:
     *  @parent - the selector container containing links,
     *  @child - the selector links,
     *  @container - selector container into which the data are loaded,
     *  @cache - (boolean) whether to cache ajax request.
     *  Cancels the link.
     *  
     *  Метод init иницализирует ajax загрузку по клику на ссылку, где:
     *  @parent - селектор контейнера, содержащего ссылки, 
     *  @child - селектор ссылки,
     *  @container - селектор контейнера, в который загружаются полученные данные,
     *  @cache - (false/true) кэшировать ли ajax запрос.
     *  Отменяет переход по ссылке.
     */
    this.init = function(parent, child, container, cache){
        $(parent).on('click', child, function(){
            var url = $(this).attr('href');
            _self.ajax(url, container, cache);
            return false;
        });
    };
    
    /*
     *  Method of loading a container according to the content transmitted by the link, wherein:
     *  @url - links to content,
     *  @container - container into which the data will be loaded,
     *  @cache - (boolean) whether to cache.
     *  
     *  Метод загружает контент в контейнер по передаваемой ссылке, где:
     *  @url - ссылка на контент,
     *  @container - контейнер в который будут загружены полученные данные,
     *  @cache - (булевый) кэшировать ли.
     */
    this.ajax = function(url, container, cache){
        $.ajax({
            'url': url,
            'cache': cache,
            'success': function(html){_self.success(html, container);}
        });
    };
    
    /*
     *  The method is called when the operation succeeds ajax request.
     *  @html - the returned data,
     *  @container - selector container into which are inserted the data @html.
     *
     *  Метод вызывающийся при успешном выполнении ajax запроса.
     *  @html - возвращаемые данные,
     *  @container - селектор контейнера, в который будут вставлены полученные данные @html.
     */
    this.success = function(html, container){
        $(container).html(html);
    };
}