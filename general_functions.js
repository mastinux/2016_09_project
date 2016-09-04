function removeElementById(id) {
    var element = document.getElementById(id);
    element.parentNode.removeChild(element);
}

function printMessage(type, msg) {
    var types = ['info', 'success', 'warning', 'danger'];
    for (var i = 0; i < types.length; i++)
        if (document.getElementById(types[i] + "-msg"))
            removeElementById(types[i] + "-msg");

    var navbar = document.getElementById("navbar");

    var div = document.createElement("div");
    div.setAttribute("id", type + "-msg");
    div.setAttribute("class", "col-lg-12");
    navbar.parentNode.insertBefore(div, navbar.nextElementSibling);

    var textDiv = document.createElement("div");
    textDiv.setAttribute("class", "alert alert-" + type + " alert-dismissible");
    textDiv.setAttribute("role", "alert");
    textDiv.innerHTML = msg;

    div.appendChild(textDiv);

    var button = document.createElement("button");
    button.setAttribute("type", "button");
    button.setAttribute("class", "close");
    button.setAttribute("data-dismiss", "alert");
    button.setAttribute("aria-label", "Close");

    textDiv.appendChild(button);

    var span = document.createElement("span");
    span.setAttribute("aria-hidden", "true");
    span.innerHTML = "&times;";

    button.appendChild(span);
}
