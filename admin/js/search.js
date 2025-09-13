// Global search functionality for admin panel

// Global functions for form submission and keypress handling
function handleGlobalSearchKeypress(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        performDirectSearch();
    }
}

function handleGlobalSearch(event) {
    event.preventDefault();
    performDirectSearch();
    return false;
}

function performDirectSearch() {
    const searchTerm = document.getElementById('globalSearchInput').value.trim();
    
    if (!searchTerm) {
        return;
    }
    
    // Check if search term looks like an order number (starts with # or is numeric)
    if (searchTerm.startsWith('#') || /^\d+$/.test(searchTerm)) {
        const orderNum = searchTerm.replace('#', '');
        window.location.href = `orders.php?search=${encodeURIComponent(orderNum)}`;
        return;
    }
    
    // Check if search term looks like an email
    if (searchTerm.includes('@')) {
        window.location.href = `users.php?search=${encodeURIComponent(searchTerm)}`;
        return;
    }
    
    // Default to product search for other terms
    window.location.href = `products.php?search=${encodeURIComponent(searchTerm)}`;
}

$(document).ready(function() {
    let searchTimeout;
    let isSearchVisible = false;
    
    const $searchInput = $('#globalSearchInput');
    const $searchForm = $('#mainSearchForm');
    
    // Only proceed if we have the search input
    if ($searchInput.length === 0) {
        return;
    }
    
    // Remove any existing search results container to prevent duplicates
    $('#search-results-container').remove();
    
    const $searchContainer = $('<div id="search-results-container" class="search-results-dropdown"></div>');
    
    // Add search results container to the main search form
    if ($searchForm.length > 0) {
        $searchForm.append($searchContainer);
    } else {
        // Fallback to the first searchform if mainSearchForm not found
        $('.searchform').first().append($searchContainer);
    }
    
    // Handle search input
    $searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            hideSearchResults();
            return;
        }
        
        // Debounce search requests
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    
    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.searchform').length) {
            hideSearchResults();
        }
    });
    
    // Handle escape key
    $searchInput.on('keydown', function(e) {
        if (e.key === 'Escape') {
            hideSearchResults();
            $(this).blur();
        }
    });
    
    function performSearch(query) {
        // Show loading state
        showLoadingState();
        
        $.ajax({
            url: 'api/search.php',
            method: 'GET',
            data: { q: query },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displaySearchResults(response.results);
                } else {
                    showErrorState(response.message || 'Search failed');
                }
            },
            error: function() {
                showErrorState('Search request failed');
            }
        });
    }
    
    function displaySearchResults(results) {
        if (results.length === 0) {
            showNoResults();
            return;
        }
        
        let html = '<div class="search-results-header">Search Results</div>';
        
        // Group results by type
        const grouped = groupResultsByType(results);
        
        Object.keys(grouped).forEach(type => {
            if (grouped[type].length > 0) {
                html += `<div class="search-group">
                    <div class="search-group-title">${capitalizeFirst(type)}s</div>`;
                
                grouped[type].forEach(result => {
                    const statusClass = getStatusClass(result.status);
                    html += `
                        <a href="${result.url}" class="search-result-item">
                            <div class="search-result-content">
                                <i class="fe ${result.icon} search-result-icon"></i>
                                <div class="search-result-text">
                                    <div class="search-result-title">${escapeHtml(result.title)}</div>
                                    <div class="search-result-subtitle">${escapeHtml(result.subtitle)}</div>
                                </div>
                                <span class="badge badge-${statusClass} search-result-status">${result.status}</span>
                            </div>
                        </a>`;
                });
                
                html += '</div>';
            }
        });
        
        $searchContainer.html(html);
        showSearchResults();
    }
    
    function groupResultsByType(results) {
        const grouped = { user: [], seller: [], product: [], order: [] };
        results.forEach(result => {
            if (grouped[result.type]) {
                grouped[result.type].push(result);
            }
        });
        return grouped;
    }
    
    function showLoadingState() {
        $searchContainer.html('<div class="search-loading">Searching...</div>');
        showSearchResults();
    }
    
    function showErrorState(message) {
        $searchContainer.html(`<div class="search-error">Error: ${escapeHtml(message)}</div>`);
        showSearchResults();
    }
    
    function showNoResults() {
        $searchContainer.html('<div class="search-no-results">No results found</div>');
        showSearchResults();
    }
    
    function showSearchResults() {
        if (!isSearchVisible) {
            $searchContainer.addClass('show');
            isSearchVisible = true;
        }
    }
    
    function hideSearchResults() {
        if (isSearchVisible) {
            $searchContainer.removeClass('show');
            isSearchVisible = false;
        }
    }
    
    function getStatusClass(status) {
        const statusMap = {
            'active': 'success',
            'approved': 'success',
            'published': 'success',
            'delivered': 'success',
            'pending': 'warning',
            'processing': 'info',
            'cancelled': 'danger',
            'inactive': 'secondary',
            'rejected': 'danger',
            'draft': 'secondary'
        };
        return statusMap[status] || 'secondary';
    }
    
    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});