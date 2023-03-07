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
            if(response) {
                rentman.activeProject = response.project;
                if (callback) {
                    callback(response);
                }
            } else {
                if (callback) {
                    callback(null);
                }
            }

        });
    },

    searchProducts: function (query,callback) {
        $.get('/actions/rentman-for-craft/api/search-products', {query:query}, function(response) {
            if(response) {
                if (callback) {
                    callback(response);
                }
            } else {
                if (callback) {
                    callback(null);
                }
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
            if (callback) {
                callback(response);
            }
        });
    },

    /*
    * updates the shooting days of active project
    */
    setProjectShootingDays: function (shooting_days, callback) {
        let data = {
            shooting_days: shooting_days,
        };
        $.post('/actions/rentman-for-craft/api/set-project-shooting-days', data, function(response) {
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
        let data = {
            projectId: projectId,
        };
        $.post('/actions/rentman-for-craft/api/copy-project', data, function(response) {
            if (callback) {
                callback(response);
            }
        });
    },

    /*
     * delete the project
     */
    deleteProject: function (projectId, callback) {
        let data = {
            projectId: projectId,
        };
        $.post('/actions/rentman-for-craft/api/delete-project', data, function(response) {
            if (callback) {
                callback(response);
            }
        });
    },

}

document.addEventListener('DOMContentLoaded', function (event) {
    rentman.init();
});



