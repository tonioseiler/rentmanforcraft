window.rentman = {
    init: function () {
        rentman.initEventListeners(document);
    },
    initEventListeners: function (context) {
        
    },

    listeners: {},

    /*
     * retreive a list of the users projects
     */
    getProjects: function(callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * get the current active project, return null if user dies not have a project
     */
    getActiveProject: function(callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * set the currenty active project
     */
    setActiveProject: function(projectId, callback) {
        if (callback) {
            callback();
        }
    },

     /*
     * create a new project
     */
     createProject: function(callback) {
        if (callback) {
            callback();
        }

    },

    /*
    * get the current active project's quantity, return null if user dies not have a project
    */
    getActiveProjectQuantity: function(projectId) {
        console.log('rentman.getActiveProjectQuantity()');
        let newQuantity = Math.floor(Math.random() * 100);
        return newQuantity;

    },

    /*
     * adds a product to the active project
     */
    addProductToProject: function(projectId,productId, quantity,callback) {
        console.log('rentman.addProductToProject()');
        if (callback) {
            console.log('callback:');
            console.log(callback);
            return callback();
        }
    },

    /*
     * removes a product to the project, if the projectId is null, adds it to the current project
     */
    removeProductFromProject: function(productId, callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * submits the active project in checkout, will change the state
     */
    submitProject: function(callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * update the active project
     */
    updateProject: function(callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * copy the project
     */
    copyProject: function(projectId, callback) {
        if (callback) {
            callback();
        }
    },

    /*
     * delete the project
     */
    deleteProject: function(projectId, callback) {
        if (callback) {
            callback();
        }
    },

}

document.addEventListener('DOMContentLoaded', function (event) {
    rentman.init();
});


