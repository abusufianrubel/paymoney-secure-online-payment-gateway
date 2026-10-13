'use strict';

$(document).ready(function() {
    // Handle CSV and PDF clicks with a single function
    $('#csv, #pdf').on('click', function(event) {
        event.preventDefault();

        // Determine the base URL based on the clicked button's ID
        var baseUrl = (this.id === 'csv') ? csvUrl : pdfUrl;
        
        // Append the query string to the base URL
        window.location = baseUrl + "?" + mapQueryParameter();
    });
});

function mapQueryParameter() {
    return $.param(
        $('form').serializeArray()
            .filter(field => field.name !== 'user') // Exclude 'user' field
            .map(field => ({
                name: field.name.replace('from', 'startfrom').replace('to', 'endto'),
                value: field.value
            }))
    );
}
