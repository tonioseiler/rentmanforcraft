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
    getProjects: function (callback) {

        let projects = [
            {
                "id": "1",
                "title": "Project one",
                "active": false,

            },
            {
                "id": "2",
                "title": "Project two",
                "active": false,

            },
            {
                "id": "3",
                "title": "Project three",
                "active": true,
            },
            {
                "id": "4",
                "title": "Project four",
                "active": false,
            },
        ];

        if (callback) {
            callback();
        } else {
            return JSON.stringify(projects);

        }
    },

    /*
     * get the current active project, return null if user dies not have a project
     */
    getActiveProject: function (callback) {
        currentProjectId = Math.floor(Math.random() * 10);
        currentProjectTitle = "My project title";
        if (callback) {
            callback();
        } else {
            let currentProject = {
                projectId: currentProjectId,
                projectTitle: currentProjectTitle,
            }
            return currentProject;
        }
    },

    /*
     * set the currenty active project
     */
    setActiveProject: function (projectId, callback) {

        if (callback) {
            callback();
        }
    },

    /*
    * create a new project
    */
    createProject: function (callback) {
        if (callback) {
            callback();
        }

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
     */
    //addProductToProject: function(projectId,productId, quantity,callback) {
    addProductToProject: function (args) {
        /*
        args:
            projectId
            productId
            quantity
            callbackNamespace
            callback
         */

        // here ajax call, on result execute and return the callback if set
        if ((args.callback)&&(args.callbackNamespace)) {
            return window[args.callbackNamespace][args.callback](args.projectId);
            //return window["rentman"][args.callback](args.projectId);
        }
    },

    /*
     * removes a product to the project, if the projectId is null, adds it to the current project
     */
    removeProductFromProject: function (productId, callback) {
        if (callback) {
            callback();
        }
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


