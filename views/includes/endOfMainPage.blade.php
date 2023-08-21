<div id="bg"></div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get a reference to the close element
        var closeButton = document.querySelector(".close");
    
        // Add a click event listener to the close element
        closeButton.addEventListener("click", function() {
            // Navigate to the desired URL when the element is clicked
            window.location.href = "/index";
        });
    });
    </script>    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="@asset("js/script.min.js")"></script>
</body>
</html>