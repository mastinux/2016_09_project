function checkEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function register(){
    var name = document.getElementById("new-name").value;
    var surname = document.getElementById("new-surname").value;
    var email = document.getElementById("new-email").value;
    var password = document.getElementById("new-password").value;
    var repeated_password = document.getElementById("new-password-repeated").value;

    // checking empty values
    if ( !name || !surname || !email || !password || !repeated_password ){
        console.log("Name, surname, email or password not inserted in registration form.");
        printMessage("warning", "Name, surname, email or password not inserted in registration form.");
        return false;
    }

    // checking email
    if ( !checkEmail(email) ){
        console.log("Invalid email.");
        printMessage("danger", "Invalid email inserted in registration form.");
        return false;
    }

    // checking match between password and repeated_password
    if (password != repeated_password) {
        console.log("Password does not match.");
        printMessage("danger", "Passwords inserted do not match in registration form.");
        return false;
    }

    return true;
}

function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    // checking empty values
    if ( !username || !password ){
        console.log("Username or password not inserted in login form.");
        printMessage("warning", "Username or password not inserted in login form.");
        return false;
    }

    // checking email
    if ( !checkEmail(username) ){
        console.log("Invalid email.");
        printMessage("danger", "Invalid email inserted in login form.");
        return false;
    }

    return true;
}