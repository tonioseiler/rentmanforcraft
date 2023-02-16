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
        let currentProjectId = Math.floor(Math.random() * 100);
        let currentProjectTitle = "My project title " + currentProjectId;
        let currentProjectQuantity = 88;
        if (callback) {
            callback();
        } else {
            let currentProject = {
                projectId: currentProjectId,
                projectTitle: currentProjectTitle,
                projectQuantity: currentProjectQuantity,
            }
            return currentProject;
        }
    },

    /*
     * set the currenty active project
     */
    setActiveProject: function (projectId, callback) {
        // here do ajax call, when done:
        console.log('rentman.setActiveProject()');
        if (callback) {
            window['app'][callback](projectId);
            //callback(projectId);
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
     * if callback is a string, we assume the namespace for the callback method is "app"
     * if callback is an array, the first element is used as the namespace and the second for the callback method
     */
    addProductToProject: function (projectId, productId, quantity, callback) {
        // here ajax call, on result execute and return the callback if set
        if (callback) {
            if (Array.isArray(callback)) {
                console.log('addProductToProject callback has namespace');
                return window[callback[0]][callback[1]](projectId);
            } else {
                console.log('addProductToProject callback does not have a namespace');
                return window['app'][callback](projectId);
            }
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


