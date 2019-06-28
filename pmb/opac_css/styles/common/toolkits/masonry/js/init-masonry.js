window.addEventListener('DOMContentLoaded', function(){
    var elem = document.querySelector('.ms-grid-container');
    if(elem){
        var msnry = new Masonry( elem, {
        // options
            itemSelector: '.ms-grid-item',
            columnWidth: 285,
            gutter: 20,
            resize: true,
            percentPosition: true,
            transitionDuration: '0.1s'
        });       
    }
});
