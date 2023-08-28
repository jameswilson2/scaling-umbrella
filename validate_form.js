function validateForm() {
    var name = document.forms["contactForm"]["name"].value;
    var email = document.forms["contactForm"]["email"].value;
    var phone = document.forms["contactForm"]["phone"].value;
    var service = document.forms["contactForm"]["service"].value;
    var address = document.forms["contactForm"]["address"].value;
    var message = document.forms["contactForm"]["message"].value;
    var captcha = document.forms["contactForm"]["captcha"].value;

    if (name === "" || email === "" || phone === "" || service === "" || address === "" || message === "" || captcha === "") {
        alert("All fields are required.");
        return false;
    }

    if (!validateEmail(email)) {
        alert("Invalid email format.");
        return false;
    }

    if (!validatePhoneNumber(phone)) {
        alert("Invalid phone number.");
        return false;
    }

    if (isNaN(captcha) || parseInt(captcha) !== <?php echo $captchaAnswer; ?>) {
        alert("Captcha verification failed.");
        return false;
    }

    return true;
}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validatePhoneNumber(phone) {
    var re = /^\d{10}$/;
    return re.test(phone);
}