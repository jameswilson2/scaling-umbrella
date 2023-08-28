require_once("library/captcha.php");
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <meta name="description" content="We’ve been providing hi-speed broadband to South Lakeland for over 10 years! Since 2010 we’ve gone superfast with our latest radio technology helping us achieve speeds over 100 Mb/s upload and download." />
    <meta name="author" content="Nadja Eberhardt | Design by Nadja | https://www.nadjaeberhardt.com/">
   
    <title>Kencomp Internet | Superfast Broadband for the South Lakes</title>

  
<link href="css/bootstrap.css" rel="stylesheet" >
<link href="css/navigation.css" rel="stylesheet" >
      <link href="css/style.css" rel="stylesheet" >
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
     <script src="validate_form.js"></script>

 
  </head>
  <body>
      
         <a href="#top"><img src="top.png" class="topimage">
		 </a>
      <div class="image-wrapper residential">
   <div class="container-fluid ">
        
        <div class="row justify-content-between topbar align-items-center">
       
        
            <div class="col text-center text-sm-end icons list-inline py-2">
           <a href="tel:01539 898 145"><img src="img/phone.png">01539 898 145</a> 
                <a href="myaccount.html"><img src="img/account.svg">My Account</a> 
           
              </div>
        
        </div>
        
      </div> 
      
       
      
<nav class="navbar navbar-expand-md navbar-light bg-dark mb-4 py-1">
  <div class="container-fluid ">
    <a class="navbar-brand" href="index.html"><img src="img/kencomp-logo.png" class="logo pb-1"></a>
      
         <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="toggler-icon top-bar"></span>
        <span class="toggler-icon middle-bar"></span>
        <span class="toggler-icon bottom-bar"></span>
      </button>
      
      

    <div class="collapse navbar-collapse " id="navbarCollapse">
      <ul class="navbar-nav mb-2 mb-md-0 justify-content-end w-100 text-center text-md-end">
        <li class="nav-item ">
          <a class="nav-link" aria-current="page" href="residential.html">Residential</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="business.html">Business</a>
        </li>
      
          <li class="nav-item">
          <a class="nav-link" href="holidayhome.html">Holiday Home</a>
        </li>
             <li class="nav-item">
          <a class="nav-link" href="services.html">Services</a>
        </li>
       
        
      </ul>
 
    </div>
  </div>
</nav>
      
    
      
 


      
      
      <div class="container-fluid intro">
      <div class="row intro justify-content-center align-items-center text-center ">
          
          <div class="col-12  py-5 px-sm-5 ">
      <h1>Contact us</h1>
              <h3> </h3>
         
          
         
          </div>
          
          
          
          
      </div>
      </div>
      
      
      </div>
      
        
      <div class="container-fluid py-4">
          
          <div class="row justify-content-center mt-4">
              <div class="col-11 col-sm-6 col-md-5 pe-sm-4"><h3>
                  <strong>Kencomp</strong></h3>
<p>Unit 1<br>
Meadowbank Business Park<br>
Shap Road<br>
Kendal<br>
                  LA9 6NY</p>
<p>
by phone on 01539 898145<br>
    by email <a href="">sales@kencomp.net</a></p>
<p>
If emailing or using the contact form, we will respond within 24 hours.</p></div>
              <div class="col-11 col-sm-6 col-md-5 ps-sm-4 mt-5 mt-sm-0">

                
  
    <form id="contactForm" action="enquiries/sendmail.php" method="post" onsubmit="return validateForm()" class=mb-5>


      

    <label class="form-label" for="name">Name:</label>
    <input class="form-control" type="text" name="name" required><br><br>

    <label class="form-label" for="email">Email Address:</label>
    <input class="form-control" type="email" name="email" required><br><br>

    <label class="form-label" for="phone">Phone Number:</label>
    <input class="form-control" type="tel" name="phone" required><br><br>

    <label class="form-label" for="service">Type of Service Required:</label>
    <input class="form-control" type="text" name="service" required><br><br>

    <label class="form-label" for="address">Address:</label>
    <input class="form-control" type="text" name="address" required><br><br>

    <label class="form-label" for="message">Message:</label>
    <textarea class="form-control" name="message" rows="4" required style="height: 10rem;"></textarea><br><br>

    <?php
    // Generate and display the captcha question
    $captchaData = Captcha::generateQuestion();
    echo '<label class="form-label" for="captcha">Captcha: What is ' . $captchaData["question"] . '?</label>';
    echo '<input class="form-control" type="hidden" name="captchaAnswer" value="' . $captchaData["answer"] . '">';
    ?>
    <input class="form-control" type="number" name="captcha" required><br><br>

    <button class="btn btn-danger mt-3" type="submit">Submit</button>

                  </form>
              
              
<p>Data sent from this form is recorded in line with our <a href="privacy.html">Privacy Policy</a>.</p>
              </div></div>

</div>
      
      
      
            
      
      
      
    
      
      
 
      
      <div class="container-fluid footer pb-2">
          
          
            <div class="row contact py-2">
              <div class="col-12 text-center">
                  <a href="">sales@kencomp.net  </a>      |   <a href="">    support@kencomp.net</a></div>
       
          
          
          </div>
          
         <div class="row pt-4">
          <div class="col-6 col-sm-3 red"><ul>
              <li><a href="index.html">Home</a></li>
                     <li><a href="residential.html">Residential</a></li>
                      <li><a href="business.html">Business</a></li>
                      <li><a href="holidayhome.html">Holiday Home</a></li>
                      <li><a href="services.html">Additional Services</a></li>
               <li><a href="myaccount.html">My Account</a></li>
                      <li><a href="contact.html">Contact</a></li>
              
              
              
              
              </ul></div>
          
            <div class="col-6 col-sm-3"><ul>
              <li><a href="faq.html">FAQ</a></li>
                     <li><a href="privacy.html">Privacy</a></li>
                      <li><a href="terms.html">T&amp;C</a></li>
                      <li><a href="acceptableuse.html">Usage</a></li>
                      <li><a href="complaints.html">Complaints</a></li>
                      <li><a href="code.html">Conduct</a></li>
              
              
              
              
              </ul></div>
          
           <div class="col-12 col-sm-6 text-center text-sm-end mt-4 mt-sm-0 red"><div><a href="https://www.ombudsman-services.org/" target="blank"><img src="img/ombudsman.png"></a> <a href="https://www.ukwispa.org/" target="blank"><img src="img/wispa.png" ></a></div></div>
          
          
          </div>
          
          <div class="row credits mt-4 pt-2 text-center text-sm-start">
              <div class="col-12 col-sm-8">
          <p>© 2023 Kencomp Internet Limited, Unit 1, Meadowbank Business Park, Shap Road, Kendal, LA9 6NY <br><a href="https://what3words.com/aura.hails.dimension" target="blank"><img src="img/What3Words.svg" class="w3w">aura.hails.dimension</a></p></div>
              <div class="col text-sm-end"><p> <a target="_blank" href="https://www.nadjaeberhardt.com/">Design by Nadja</a></p></div>
          
          
          </div>
      
      
      
      </div>
      

    <script src="js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
      
      <script>
  AOS.init();
</script>
      
      
  </body>
</html>