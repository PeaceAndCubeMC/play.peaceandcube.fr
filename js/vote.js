function submitVote() {
    var username = document.getElementById("username").value;
    $.ajax({
        url: "./dovote.php",
        type: "POST",
        data: { username: username },
        success: function(data) {
            var obj = JSON.parse(data);
            if (obj.state == "SUCCESS") {
                var icon = "success";
            } else {
                var icon = "warning";
            }

            Swal.fire({
                icon: icon,
                text: obj.message
            });
        }
    });
}
