<div id="bg"></div>
<script>
    // Define pages that do not require a fragment check
    var excludedPaths = ['/', '/index'];

    // Get the current path and fragment
    var currentPath = window.location.pathname;
    var currentFragment = window.location.hash.substring(1);

    // Check if the current path is not in the excluded paths and if the fragment is missing
    if (!excludedPaths.includes(currentPath) && !currentFragment) {
        // Redirect to the same path with a #notfound fragment
        window.location.href = currentPath + '#notfound';
    }
</script>
<script src="@asset("js/redirectScript.js")"></script>    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="@asset("js/script.min.js")"></script>
</body>
</html>