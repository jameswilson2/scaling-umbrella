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

    let settings={backgroundColor:"rgba(1, 1, 1, 0.5)",filterBrightness:0,timeOnScreen:100},u=document.querySelector("*"),s=document.createElement("style"),a=document.createElement("div"),m="http://www.w3.org/2000/svg",g=document.createElementNS(m,"svg"),c=document.createElementNS(m,"circle");document.head.appendChild(s),s.innerHTML="@keyframes swell{to{transform:rotate(360deg)}}",a.setAttribute("id","loading"),a.setAttribute("style","background-color:"+settings.backgroundColor+";color:"+settings.backgroundColor+";display:flex;align-items:center;justify-content:center;position:fixed;top:0;height:100vh;width:100vw;z-index:2147483647"),document.body.prepend(a),g.setAttribute("style","height:50px;filter:brightness("+settings.filterBrightness+");animation:.3s swell infinite linear"),g.setAttribute("viewBox","0 0 100 100"),a.prepend(g),c.setAttribute("cx","50"),c.setAttribute("cy","50"),c.setAttribute("r","35"),c.setAttribute("fill","none"),c.setAttribute("stroke","currentColor"),c.setAttribute("stroke-dasharray","165 57"),c.setAttribute("stroke-width","10"),g.prepend(c),u.style.pointerEvents="none",u.style.userSelect="none",u.style.cursor="wait",window.onload=()=>{(u.style.pointerEvents=""),(u.style.userSelect=""),(u.style.cursor="")}
}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

function validatePhoneNumber(phone) {
    var re = /^(((\+44\s?\d{4}|\(?0\d{4}\)?)\s?\d{3}\s?\d{3})|((\+44\s?\d{3}|\(?0\d{3}\)?)\s?\d{3}\s?\d{4})|((\+44\s?\d{2}|\(?0\d{2}\)?)\s?\d{4}\s?\d{4}))(\s?\#(\d{4}|\d{3}))?$/;
    return re.test(phone);
}

function highlightField(fieldName) {
    document.forms["contactForm"][fieldName].style.borderColor = "red";
}

function displayError(message) {
    var loadingDiv = document.getElementById("loading");
    var errorContainer = document.getElementById("errorContainer");
    loadingDiv.style.display = "hidden";
    errorContainer.innerHTML = message;
    errorContainer.style.display = "block";
}

function scrollToTop() {
    window.scrollTo(0, 0);
}

