$(document).ready(function() {
    var baseDir = prestashop.urls.base_url;
    var languageIsoCode = prestashop.language.iso_code;

    // Mapowanie języków na odpowiednie kontrolery wyszukiwania
    var searchControllerMap = {
        'en': 'search',
        'de': 'suche',
        // dodaj inne języki, jeśli to konieczne
    };

    // Pokaż topbar po kliknięciu przycisku Search
    $('.search-button').on('click', function() {
        $('#search-topbar').slideDown();
        $('#searchbyrefmpn-input').focus();
    });

    // Ukryj topbar po kliknięciu przycisku zamknięcia
    $('.close-button').on('click', function() {
        $('#search-topbar').slideUp();
    });

    $('#searchbyrefmpn-input').on('input', function() {
        var searchQuery = $(this).val();
        if (searchQuery.length > 2) {
            $.ajax({
                url: baseDir + 'modules/searchbyrefmpn/ajax_search.php',
                type: 'POST',
                data: { query: searchQuery, action: 'suggest' },
                success: function(response) {
                    console.log("AJAX response for suggestions:", response);
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            console.log("Suggestions received:", jsonResponse.suggestions);
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
                    console.error("AJAX error:", error);
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
                    console.log("AJAX response for search:", response);
                    try {
                        var jsonResponse = JSON.parse(response);
                        if (jsonResponse.success) {
                            console.log("Redirecting to:", jsonResponse.url);
                            window.location.href = jsonResponse.url;
                        } else {
                            console.log("No specific results, redirecting to standard search");
                            var searchController = searchControllerMap[languageIsoCode] || 'search';
                            window.location.href = baseDir + searchController + '?s=' + encodeURIComponent(searchQuery);
                        }
                    } catch (e) {
                        console.error("Error parsing JSON:", e);
                        var searchController = searchControllerMap[languageIsoCode] || 'search';
                        window.location.href = baseDir + searchController + '?s=' + encodeURIComponent(searchQuery);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", error);
                    var searchController = searchControllerMap[languageIsoCode] || 'search';
                    window.location.href = baseDir + searchController + '?s=' + encodeURIComponent(searchQuery);
                }
            });
        }
    });
});
