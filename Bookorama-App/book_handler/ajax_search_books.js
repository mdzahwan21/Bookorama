document.addEventListener("DOMContentLoaded", function () {
    // Function to handle AJAX requests
    function fetchData() {
        var searchKey = document.getElementById('search_key').value;
        var category = document.getElementById('category').value;
        var minPrice = document.getElementById('min_price').value;
        var maxPrice = document.getElementById('max_price').value;

        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('book_results').innerHTML = xhr.responseText;
            }
        };

        var url = 'ajax_fetch_books.php?' +
            'search_key=' + encodeURIComponent(searchKey) +
            '&category=' + encodeURIComponent(category) +
            '&min_price=' + encodeURIComponent(minPrice) +
            '&max_price=' + encodeURIComponent(maxPrice);

        xhr.open('GET', url, true);
        xhr.send();
    }

    // Trigger AJAX requests on form input change
    var formInputs = document.querySelectorAll('#search_key, #category, #min_price, #max_price');
    formInputs.forEach(function (input) {
        input.addEventListener('input', fetchData);
    });

    // Initial data load
    fetchData();
});