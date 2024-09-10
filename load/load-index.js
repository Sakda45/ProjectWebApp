// load-index.js
document.addEventListener("DOMContentLoaded", function() {
    // ฟังก์ชันสำหรับโหลดเนื้อหาใหม่
    window.loadPage = function(page) {
        fetch(page)
            .then(response => response.text())
            .then(data => {
                document.getElementById('page-index').innerHTML = data;
            })
            .catch(error => console.error('Error loading page:', error));
    };
});
