window.rentman = {
    init: function () {
        rentman.initEventListeners(document);
    },
    initEventListeners: function (context) {
        
    },

    listeners: {},

    addProductToProject: function(receivedDataArray) {
        console.log(receivedDataArray);
    },

}

document.addEventListener('DOMContentLoaded', function (event) {
    rentman.init();
});


