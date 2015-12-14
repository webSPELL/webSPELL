function validateForm() {
    var x = document.forms["linkus"]["name"].value;
    if (x == null || x == "") {
        alert("Name must be filled out");
        return false;
    }
}