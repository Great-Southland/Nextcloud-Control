function optionCheck(that) {
    if (that.value == "add-to-group" || that.value == "remove-from-group") {
        document.getElementById("wp-roles").style.display = "inline-block";
    } else {
        document.getElementById("wp-roles").style.display = "none";
    }
}
