<img src="resources/img/plugin-logo.png" width="100" height="100">

# Rentman for CraftCMS Plugin API Guide

The Rentman Plugin for CraftCMS provides a JavaScript API to manage projects and perform associated actions. Below is a detailed guide on how to use this API:

## **Setup**

First, ensure that the Rentman Plugin is properly installed and configured in your CraftCMS instance.

## **Initialization**

When your webpage is loaded, the API will automatically initialize. No additional steps are required.
The API calls are accessible through the global **window.rentman** variable.

## **Methods**

### **General Project Management**

1. **Get User Projects**:
    ```javascript
    rentman.getUserProjects(callback);
    ```

2. **Get Active Project**:
   Retrieve the current active project. If no active project is found, it will return `null`.
    ```javascript
    rentman.getActiveProject(callback);
    ```

3. **Set Active Project**:
   Set a project as active using its ID.
    ```javascript
    rentman.setActiveProject(projectId, callback);
    ```

4. **Create New Project**:
    ```javascript
    rentman.createProject(callback);
    ```

5. **Submit Project**:
   Submit the active project for checkout.
    ```javascript
    rentman.submitProject(callback);
    ```

6. **Update Active Project**:
    ```javascript
    rentman.updateProject(callback);
    ```

7. **Copy a Project**:
   Create a duplicate of a given project.
    ```javascript
    rentman.copyProject(projectId, callback);
    ```

8. **Delete a Project**:
   Delete a project using its ID.
    ```javascript
    rentman.deleteProject(projectId, callback);
    ```

9. **Download Project as PDF**:
   Opens the project in a new tab as a PDF.
    ```javascript
    rentman.downloadProjectPdf(projectId);
    ```

### **Product Management within Projects**

1. **Search Products**:
    ```javascript
    rentman.searchProducts(query, callback);
    ```

2. **Set Product Quantity in Project**:
   Add a product or modify a product's quantity in the active project.
    ```javascript
    rentman.setProjectProductQuantity(productId, quantity, callback);
    ```

### **Project Details Management**

1. **Set Shooting Days**:
   Update the number of shooting days for the active project.
    ```javascript
    rentman.setProjectShootingDays(shooting_days, callback);
    ```

## **Callbacks**

Most methods have a `callback` parameter. This callback function is executed when the API request is completed, and the result is passed to this function. For example:

```javascript
rentman.getActiveProject(function(result) {
    if(result) {
        console.log("Active project:", result.project);
    } else {
        console.log("No active project found.");
    }
});
```

## **Notes**

- Handle responses in your callback functions to ensure you're taking proper actions based on the results, especially for error handling or when no data is returned.


# Example using jQuery

**Note**: This is just an example documentation.

## Imports

```javascript
import jquery from 'jquery';
window.$ = window.jQuery = jquery;
```




## Initialization

On document's content being loaded:

```javascript
document.addEventListener('DOMContentLoaded', function (event) {
    app.init();
});
```

## Event Listeners

The app initializes various event listeners:

- `productQuantityChange`: Listens to changes in product quantity.
- `projectShootingDaysChange`: Listens to changes in the shooting days of a project.

... and many others, see below.

## Methods

These are functions used to manage projects:

- `projectDeleteConfirm`: Used to confirm deletion of a project.
- `projectUpdateTotals`: Update the totals displayed for a project.
- `projectUpdateProductsQuantities`: Updates the quantities of products for a project.
- `delayedProjectUpdateProductsQuantities`: Delays the update of product quantities for a project.

... and many others, see below.

## Main App Object

The primary object for the website's functionality is the `app` object. It contains all the methods and event listeners used to manage projects.  
[Here](examples/app.js) is an example using jQuery.


---

To utilize this documentation, consider following the structure and referencing the specific sections as required when integrating the JavaScript code into a project.