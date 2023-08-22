document.addEventListener("DOMContentLoaded", function() {
    // Get references to the login section and the close button
    var loginSection = document.querySelector("#login");
    var closeButton = document.querySelector(".close");
  
    // Add a click event listener to the document
    document.addEventListener("click", function(event) {
      // Get the current fragment identifier from the URL
      var currentFragment = window.location.hash;
  
      // Check if the fragment identifier is present and not empty
      if (currentFragment && currentFragment.length > 1) {
        // Continue with normal behavior
        return;
      } else {
        // Redirect to the desired page
        window.location.href = "/index";
      }
    });
  
    // Add a click event listener to the close button
    closeButton.addEventListener("click", function(event) {
      // Prevent the default behavior of the close button
      event.preventDefault();
      
      // Navigate to the desired URL when the close button is clicked
      window.location.href = "/index";
    });
});