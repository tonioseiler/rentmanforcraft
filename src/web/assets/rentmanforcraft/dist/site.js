window.rentman = {
    init: function () {
        rentman.initEventListeners(document);
    },
    initEventListeners: function (context) {
        
    },

    listeners: {},

    addProductToProject: function() {
        alert('hello word');
    },

}

document.addEventListener('DOMContentLoaded', function (event) {
    rentman.init();
});


