$(document).ready(function() {
    var baseDir = prestashop.urls.base_url;

    $('#searchbyrefmpn-input').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 2) {
            $.ajax({
                url: baseDir + 'modules/searchbyrefmpn/ajax_search.php',
                type: 'POST',
                data: { query: searchQuery, action: 'suggest' },
                success: function(response) {
                    console.log("AJAX response for suggestions:", response);  // Logowanie odpowiedzi
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            console.log("Suggestions received:", jsonResponse.suggestions);  // Logowanie sugestii
                            var suggestions = jsonResponse.suggestions;
                            var suggestionsList = $('#suggestions-list');
                            suggestionsList.empty();
                            suggestions.forEach(function(suggestion) {
                                suggestionsList.append(
                                    '<li data-url="' + suggestion.link + '">'
                                    + '<img src="' + suggestion.image + '" alt="' + suggestion.name + '" style="width:50px;height:50px;margin-right:10px;">'
                                    + suggestion.name + ' (' + (suggestion.reference ? suggestion.reference : '') + ' / ' + (suggestion.supplier_reference ? suggestion.supplier_reference : '') + ' / ' + (suggestion.mpn ? suggestion.mpn : '') + ')</li>'
                                );
                            });
                            suggestionsList.show();
                        } else {
                            console.error("Error in suggestions response:", jsonResponse.error);
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);  // Logowanie błędów AJAX
                }
            });
        } else {
            $('#suggestions-list').hide();
        }
    });

    $('#suggestions-list').on('click', 'li', function() {
        window.location.href = $(this).data('url');
    });

    $('#searchbyrefmpn-form').submit(function(e) {
        e.preventDefault();
        var searchQuery = $('#searchbyrefmpn-input').val();
        if (searchQuery) {
            $.ajax({
                url: baseDir + 'modules/searchbyrefmpn/ajax_search.php',
                type: 'POST',
                data: { query: searchQuery },
                success: function(response) {
                    console.log("AJAX response for search:", response);  // Logowanie odpowiedzi
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            console.log("Redirecting to:", jsonResponse.url);  // Logowanie przekierowania
                            window.location.href = jsonResponse.url;
                        } else {
                            alert('Product not found');
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        alert('An error occurred while processing your request.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);  // Logowanie błędów AJAX
                    alert('An error occurred while processing your request.');
                }
            });
        }
    });
});
