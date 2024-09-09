// ใช้ JavaScript โหลดไฟล์ navbar.html
document.addEventListener("DOMContentLoaded", function() {
    fetch('../page/index.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('page-index').innerHTML = data;
        })
        window.loadPage = function(page) {
            fetch(page)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('page-index').innerHTML = data;
                })
                .catch(error => console.error('Error loading page:', error));
        };
});
