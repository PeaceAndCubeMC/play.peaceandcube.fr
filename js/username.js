document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("submit").addEventListener("click", function() {
        setUsername();
    });
    getUsername();
});

function getUsername() {
    var username = localStorage.getItem("username");
    if (username) {
        document.getElementById("username").value = username;
        document.getElementById("remember_me").checked = true;
    }
}

function setUsername() {
    var username = document.getElementById("username").value;
    var rememberMe = document.getElementById("remember_me").checked;
    if (rememberMe == true) {
        localStorage.setItem("username", username);
    } else {
        localStorage.removeItem("username");
    }
}
