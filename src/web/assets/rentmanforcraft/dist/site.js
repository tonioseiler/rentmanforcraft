window.rentman = {

    activeProject: null,

    init: function () {
        rentman.initEventListeners(document);
        rentman.initAjaxRequests();
    },

    initAjaxRequests: function() {
        let tokenName = window.csrfTokenName;
        let tokenValue = window.csrfTokenValue;
        let data = {};
        data[tokenName] = tokenValue;
        $.ajaxSetup({
            data: data,
            dataType: "json",
            cache: false
        });
    },

    initEventListeners: function (context) {

    },

    listeners: {},

    /*
     * retreive a list of the users projects
     */
    getUserProjects: function (callback) {
        $.get('/actions/rentman-for-craft/api/get-user-projects', function(response) {
            if (callback) {
                callback(response);
            }
        });
    },

    /*
     * get the current active project, return null if user dies not have a project
     * if callback is a string, we assume the namespace for the callback method is "app"
     * if callback is an array, the first element is used as the namespace and the second for the callback method
     */
    getActiveProject: function (callback) {
        $.get('/actions/rentman-for-craft/api/get-active-project', function(response) {
            rentman.activeProject = response.project;
            if (callback) {
                callback(response);
            }
        });
    },

    /*
     * set the currenty active project
     * if callback is a string, we assume the namespace for the callback method is "app"
     * if callback is an array, the first element is used as the namespace and the second for the callback method
     */
    setActiveProject: function (projectId, callback) {
        $.post('/actions/rentman-for-craft/api/set-active-project', {projectId:projectId}, function(response) {
            rentman.activeProject = response;
            if (callback) {
                callback(response);
            }
        });
    },

    /*
    * create a new project
    */
    createProject: function (callback) {
        $.post('/actions/rentman-for-craft/api/create-project', {}, function(response) {
            if (callback) {
                callback(response);
            }
        });
    },

    /*
    * get the current active project's quantity, return null if user dies not have a project
    */
    getActiveProjectQuantity: function (projectId) {
        // here ajax call, on result return the quantiy of products in this project
        let newQuantity = Math.floor(Math.random() * 100);
        return newQuantity;
    },

    /*
     * adds a product to the active project
     * if callback is a string, we assume the namespace for the callback method is "app"
     * if callback is an array, the first element is used as the namespace and the second for the callback method
     */
    setProjectProductQuantity: function (productId, quantity, callback) {
        let data = {
            productId: productId,
            quantity: quantity
        };
        $.post('/actions/rentman-for-craft/api/set-project-product-quantity', data, function(response) {
            console.log(response);
            if (callback) {
                callback(response);
            }
        });
    },

    /*
     * submits the active project in checkout, will change the state
     */
    submitProject: function (callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * update the active project
     * probably not used at the moment
     */
    updateProject: function (callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * copy the project
     */
    copyProject: function (projectId, callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * delete the project
     */
    deleteProject: function (projectId, callback) {
        if (callback) {
            callback();
        }
    },

}

document.addEventListener('DOMContentLoaded', function (event) {
    rentman.init();
});



