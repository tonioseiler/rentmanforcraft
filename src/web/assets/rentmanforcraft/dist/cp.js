document.addEventListener('DOMContentLoaded', function (event) {
    $('#submit-to-rentman').on("click", function (e) {
        let t = $(e.currentTarget);
        let url = t.data('action');
        let projectId = t.data('project');
        let data =  {};
        data[window.Craft.csrfTokenName] = window.Craft.csrfTokenValue;
        data['projectId'] = projectId;
            
        $.post(url, data, function(response) {
            Craft.cp.displayNotice(response.message);
        });
    });
});
