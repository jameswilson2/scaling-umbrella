function validateForm() {
    var name = document.forms["contactForm"]["name"].value;
    var email = document.forms["contactForm"]["email"].value;
    var phone = document.forms["contactForm"]["phone"].value;
    var service = document.forms["contactForm"]["service"].value;
    var address = document.forms["contactForm"]["address"].value;
    var message = document.forms["contactForm"]["message"].value;
    var captcha = document.forms["contactForm"]["captcha"].value;

    var errorMessage = "";

    if (name === "") {
        highlightField("name");
        errorMessage += "Name is required.<br>";
    }

    if (!validateEmail(email)) {
        highlightField("email");
        errorMessage += "Invalid email format.<br>";
    }

    if (!validatePhoneNumber(phone)) {
        highlightField("phone");
        errorMessage += "Invalid phone number.<br>";
    }

    if (service === "") {
        highlightField("service");
        errorMessage += "Service is required.<br>";
    }

    if (address === "") {
        highlightField("address");
        errorMessage += "Address is required.<br>";
    }

    if (message === "") {
        highlightField("message");
        errorMessage += "Message is required.<br>";
    }

    if (errorMessage !== "") {
        displayError(errorMessage);
        scrollToTop();
        return false; // Cancel form submission
    }

}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validatePhoneNumber(phone) {
    var re = /^\d{11}$/;
    return re.test(phone);
}

function highlightField(fieldName) {
    document.forms["contactForm"][fieldName].style.borderColor = "red";
}

function displayError(message) {
    var errorContainer = document.getElementById("errorContainer");
    errorContainer.innerHTML = message;
    errorContainer.style.display = "block";
}

function scrollToTop() {
    window.scrollTo(0, 0);
}

