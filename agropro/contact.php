<!DOCTYPE html>
<html lang="en">
   <head>
    
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
     
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      
      <title>Smart_farming</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
   
      <link rel="stylesheet" href="css/bootstrap.min.css">

      <link rel="stylesheet" href="css/style.css">
    
      <link rel="stylesheet" href="css/responsive.css">
      
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
     
      <link rel="stylesheet" href="css/owl.carousel.min.css">
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <link rel="stylesheet" href="css/bootstrap-datepicker.min.css">
    
   </head>

   <body class="main-layout inner_page">
   
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
   
      <div class="full_bg">
        
         <?php include('navbar.html'); ?>
       
      </div>
      
      <div class="contact">
         <div class="container">
            <div class="row">
               <div class="col-md-12 ">
                  <div class="titlepage text_align_center">
                     <span>Our Contact</span>
                     <h2>Requst to Approve Project</h2>
                  </div>
               </div>
               <div class="col-md-8 offset-md-2">
                  <form id="request" class="main_form">
                     <div class="row">
                        <div class="col-md-12 ">
                           <input class="form_control" placeholder="Your Name" type="type" name=" Name"> 
                        </div>
                        <div class="col-md-12">
                           <input class="form_control" placeholder="Phone Number" type="type" name="Phone Number">                          
                        </div>
                          <div class="col-md-12">
                           <input class="form_control" placeholder="Your Email" type="type" name="Email">                          
                        </div>
                        <div class="col-md-12">
                           <input class="textarea" placeholder="Message" type="type" name="message"> 
                        </div>
                        <div class="col-md-12">
                           <div class="group_btn">
                           <button class="send_btn">Send</button>
                            <button class="send_btn">location</button>
                         </div>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
         </div>
          <div class="map-responsive">
            <iframe src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA0s1a7phLN0iaD6-UE7m4qP-z21pH0eSc&amp;q=BracUniversity+Dhaka+Bangladesh" width="600" height="430" frameborder="0" style="border:0; width: 100%;" allowfullscreen=""></iframe>
         </div>
      </div>
      
      <?php include 'footer.php'; ?>

      
      <script src="js/jquery.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
      <script src="js/jquery-3.0.0.min.js"></script>
      <script src="js/owl.carousel.min.js"></script>
      <script src="js/bootstrap-datepicker.min.js"></script>
      <script src="js/custom.js"></script>
   </body>
</html>