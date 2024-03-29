window.app = {
    init: function () {
        app.initEventListeners(document);
        window.activeProjectId = 0;
        window.runningRequestToCreateProject = false;
        window.items = null;
        rentman.getActiveProject(function (e) {
            if (e) {
                if (e.items) {
                    window.items = e.items;
                }
                if (e.project) {
                    window.activeProjectId = e.project.id;
                    if ($('body').hasClass('logged-in')) {
                        rentman.getUserProjects(function (projects) {
                            if (projects.length > 1) {
                            //...
                            }
                        });
                    }
                    app.methods.updateQuantityBubble(e.totals.totalQuantity);
                    if ($('body').hasClass('cart')) {
                        app.methods.projectUpdateTotals(e);
                    }
                } else {
                    app.methods.updateQuantityBubble(0);
                    if ($('body').hasClass('cart')) {
                        app.methods.projectUpdateTotals(e);
                    }
                }
            }

        });
    },
    initEventListeners: function (context) {
        app.listeners.productQuantityChange();
        app.listeners.projectShootingDaysChange();
        app.listeners.projectChooseNewActive();
        app.listeners.projectCopy();
        app.listeners.projectDelete();
        app.listeners.projectCreate();
        app.listeners.projectEditor();
        app.listeners.projectActivate();
        app.listeners.projectSubmit();
        app.listeners.projectDownloadPdf();
        app.listeners.searchProducts();
    },
    listeners: {
        productQuantityChange: function () {
            $('.product-quantity').each(function (index) {
                $(this).off();
                $(this).on("change", function (e) {
                    app.methods.projectUpdateProductsQuantities(
                        $(this).data('product-id'),
                        parseInt($(this).val())
                    );
                });
            });
            $('.product-quantity-plus').each(function (index) {
                $(this).off();
                $(this).on("click", function (e) {
                    let quantity = parseInt($(this).siblings('.product-quantity').val());
                    quantity++;
                    $(this).siblings('.product-quantity').val(quantity);
                    app.methods.projectUpdateProductsQuantities(
                        $(this).siblings('.product-quantity').data('product-id'),
                        quantity
                    );
                });
            });
            $('.product-quantity-minus').each(function (index) {
                $(this).off();
                $(this).on("click", function (e) {
                    let quantity = parseInt($(this).siblings('.product-quantity').val());
                    if (quantity > 0) {
                        quantity--;
                        $(this).siblings('.product-quantity').val(quantity);
                        app.methods.projectUpdateProductsQuantities(
                            $(this).siblings('.product-quantity').data('product-id'),
                            quantity
                        );
                    }
                });
            });
        },
        projectShootingDaysChange: function () {
            $('#shooting-days').on("change", function (e) {
                rentman.setProjectShootingDays($('#shooting-days').val(), function (e) {
                    app.methods.projectUpdateTotals(e);
                });
            });
        },
        projectChooseNewActive: function () {
            if ($('body').hasClass('projects')) {
                $('.projects-list.drafts li').each(function (i) {
                    let projectId = $(this).data('project-id');
                    $(this).find('.project-set-active').on('click', function () {
                        rentman.setActiveProject(projectId, function (e) {
                            document.location = '/projekt';
                        });
                    });

                });
            }
        },
        projectDelete: function () {
            if ($('body').hasClass('projects')) {
                $('.projects-list.drafts li').each(function (i) {
                    let projectId = $(this).data('project-id');
                    $(this).find('.project-delete').on('click', function () {
                        app.methods.projectDeleteConfirm(projectId);
                    });
                });
            }
        },
        projectCopy: function () {
            if ($('body').hasClass('projects')) {
                $('.projects-list li').each(function (i) {
                    let projectId = $(this).data('project-id');
                    $(this).find('.project-copy').on('click', function () {
                        rentman.copyProject(projectId, function (e) {
                            window.location.reload();
                        });
                    });
                });
            } else if ($('body').hasClass('project')) {
                $('.project-copy').on('click', function () {
                    rentman.copyProject($(this).data('project-id'), function (e) {
                        document.location = '/projekte';
                    });
                });
            }
        },
        projectCreate: function () {
            if ($('body').hasClass('projects')) {
                $('.project-new').on('click', function () {
                    rentman.createProject(function (e) {
                        rentman.setActiveProject(e.project.id, function (e) {
                            document.location = '/projekt-bearbeiten';
                        });
                    });
                });
            }
        },
        projectEditor: function () {
            if ($('body').hasClass('editProject')) {
                if (
                    ($('#title').val() == 'New project')
                    ||
                    ($('#title').val() == '')
                ) {
                    $('#title').focus().select();
                }
            }
        },
        projectActivate: function () {
            if ($('body').hasClass('project')) {
                $('.project-setactive').on('click', function () {
                    rentman.setActiveProject($(this).data('project-id'), function (e) {
                        document.location = '/projekt/';
                    });
                });
            }
        },
        projectSubmit: function () {
            if ($('body').hasClass('project')) {
                $('#project-submit').submit(function (event) {
                    $('#project-submit-button-container').addClass('hidden');
                    $('#project-submit-spinner').removeClass('hidden');
                });
            }
        },
        projectDownloadPdf: function () {
            if (($('body').hasClass('projects')) || ($('body').hasClass('project'))) {

                if ($('body').hasClass('projects')) {
                    $('.projects-list li').each(function (i) {
                        let projectId = $(this).data('project-id');
                        $(this).find('.project-pdf').on('click', function () {
                            rentman.downloadProjectPdf(projectId);
                        });
                    });
                } else {
                    $('.project-pdf').on('click', function () {
                        rentman.downloadProjectPdf($(this).data('project-id'));
                    });
                }
            }
        },
        searchProducts: function () {
            if ($('body').hasClass('category') || $('body').hasClass('product')) {
                $('#search-products').on('submit', function (e) {
                    e.preventDefault();
                    if ($('#search-product').val().length > 2) {
                        $('.search-no-results').addClass('hidden');
                        $('.search-products-list').addClass('hidden');
                        $('.title-of-products-list').html('Search results: <i>' + $('#search-product').val() + '</i>');
                        $('.products-list').remove();
                        $('.cat-name').remove();
                        $('.equipment-item').remove();
                        $('.accessories').remove();
                        $('.listitem:not(.search-header):not(#search-result-product-template)').remove();
                        $('.search-waiting').removeClass('hidden');
                        rentman.getActiveProject(function (e) {
                            if (e) {
                                if (e.items) {
                                    window.items = e.items;
                                }
                            }
                            rentman.searchProducts($('#search-product').val(), function (products) {
                                $('.search-waiting').addClass('hidden');
                                if (products.length == 0) {
                                    $('.search-no-results').removeClass('hidden');
                                } else {
                                    $('.search-products-list').removeClass('hidden');
                                    let productTemplate = $('#search-result-product-template');
                                    products.forEach(product => {
                                        let clone = productTemplate.clone().removeAttr('id');
                                        clone.find('.product-title a').attr('href', product.url);
                                        clone.find('.product-title a').html(product.displayname);
                                        clone.find('.weight').html(product.weight);
                                        clone.find('.price').html(app.methods.formatPrice(product.price, false));
                                        clone.find('.product-quantity').attr('data-product-id', product.id);
                                        if (window.items != null) {
                                            if (window.items.length > 0) {
                                                let productQuantityInCart = 0;
                                                window.items.forEach(productInCart => {
                                                    if (product.id == productInCart.productId) {
                                                        productQuantityInCart = productInCart.quantity;
                                                    }
                                                });
                                                clone.find('[data-product-id="' + product.id + '"]').val(productQuantityInCart);
                                            }
                                        }
                                        clone.removeClass('hidden');
                                        $('.search-products-list').append(clone);
                                    });
                                    app.listeners.productQuantityChange();
                                }
                            });
                        });
                    }
                });
            }
        },
    },
    methods: {
        projectDeleteConfirm: function (projectId) {
            $('#confirm-overlay').removeClass('hidden');
            $('#confirm-overlay').off();
            $('#confirm-overlay').on("click", function (e) {
                $('#confirm-overlay').addClass('hidden');
            });
            $('#confirm-overlay button').off();
            $('#confirm-overlay button').on("click", function (e) {
                rentman.deleteProject(projectId, function (e) {
                    window.location.reload();
                });
            });
            $('.confirm-overlay-message-container').off();
            $('.confirm-overlay-message-container').on("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
            });
        },
        projectUpdateTotals: function (e) {
            let daysHeaderUnit = 'Tag';
            if (e.project.shooting_days > 1) daysHeaderUnit = 'Tage';
            $('.listitem.header .total').html('Total (' + e.project.shooting_days + ' ' + daysHeaderUnit + ')');
            let newProjectTotal = app.methods.formatPrice(e.project.price, true);
            $('#cart-total-price strong').html(newProjectTotal)
            e.items.forEach(product => {
                let newProductTotal = app.methods.formatPrice(product.price, true);
                $('.listitem[data-product-id="' + product.productId + '"] .total').html(newProductTotal);
            });
            $('#current-cart .listitem').each(function (index) {
                if ($(this).find('.product-quantity').val() == 0) {
                    $(this).remove();
                }
            });
        },
        projectUpdateProductsQuantities: function (productId, quantity) {
            if (rentman.activeProject) {
                rentman.setProjectProductQuantity(productId, quantity, function (e) {
                    app.methods.updateQuantityBubble(e.totals.totalQuantity);
                    app.methods.projectUpdateTotals(e);
                });
            } else {
                if (window.runningRequestToCreateProject == false) {
                    window.runningRequestToCreateProject = true;
                    rentman.createProject(function (e) {
                        rentman.setActiveProject(e.project.id, function (e) {
                            rentman.setProjectProductQuantity(productId, quantity, function (e) {
                                app.methods.updateQuantityBubble(e.totals.totalQuantity);
                                app.methods.projectUpdateTotals(e);
                                window.runningRequestToCreateProject = false;
                            });
                        });
                    });
                } else {
                    // we are waiting for the project creation
                    app.methods.delayedProjectUpdateProductsQuantities(productId, quantity);
                }
            }
        },
        delayedProjectUpdateProductsQuantities: function (productId, quantity) {
            if (window.runningRequestToCreateProject == true) {
                setTimeout(() => {
                    app.methods.projectUpdateProductsQuantities(productId, quantity);
                }, 50);
            } else {
                app.methods.projectUpdateProductsQuantities(productId, quantity);
            }
        },
        updateQuantityBubble: function (quantity) {
            $('#project-quantity').attr('data-quantity', quantity);
            if (quantity > 0) {
                $('#project-quantity').addClass('cart-not-empty');
            } else {
                $('#project-quantity').removeClass('cart-not-empty');
            }
        },
        formatPrice: function (price, decimals = true) {
            price = Intl.NumberFormat('de-CH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(price);
            return price;
        }
    }
};