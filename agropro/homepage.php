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
  
   <body class="main-layout">
      
      <div class="loader_bg">
         <div class="loader"><img src="images/loading.gif" alt="#"/></div>
      </div>
         <div class="full_bg">
            <header class="header-area">
               <div class="container-fluid">
                  <div class="row d_flex">
                   
                     <div class="col-md-2 col-sm-3">
                        <div class="logo">
                           <a href="login.php">Smart<span>Farming</span></a>
                        </div>
                     </div>
                     
                     
                     <div class="col-md-10 padd_0">
                        <ul class="email text_align_right d-flex justify-content-end align-items-center gap-3" style="list-style: none; margin: 0;">
                           <li><a class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>" href="about.php">About</a></li>
                           <li><a href="login.php">Login</a></li>
                           <li><a href="profile.php"><i class="fa fa-search" aria-hidden="true"></i></a></li>
                        </ul>
                     </div>
                  </div>
               </div>
            </header>
         </div>
         
         <div class="slider_main">
           
             <div id="banner1" class="carousel slide carousel-fade" data-ride="carousel" data-interval="6000">
                              <ol class="carousel-indicators">
                                 <li data-target="#banner1" data-slide-to="0" class="active"></li>
                                 <li data-target="#banner1" data-slide-to="1"></li>
                                 <li data-target="#banner1" data-slide-to="2"></li>
                              </ol>
                              <div class="carousel-inner" role="listbox">
                                 <div class="carousel-item active">
                                    <picture>
                                       <source srcset="images/banner.jpg" >
                                     
                                       <img srcset="images/banner.jpg" alt="responsive image" class="d-block img-fluid">
                                    </picture>
                                    <div class="carousel-caption relative">
                                       
                                    </div>
                                 </div>
                                 
                                 <div class="carousel-item">
                                    <picture>
                                     
                                       <img srcset="images/banner.jpg" alt="responsive image" class="d-block img-fluid">
                                    </picture>
                                    <div class="carousel-caption relative">
                                       
                                    </div>
                                 </div>
                               
                                 <div class="carousel-item">
                                    <picture>
                                       <source srcset="images/banner.jpg" >
                                       <source srcset="images/banner.jpg" >
                                       <source srcset="images/banner.jpg" >
                                       <img srcset="images/banner.jpg" alt="responsive image" class="d-block img-fluid">
                                    </picture>
                                    <div class="carousel-caption relative">
                                       
                                    </div>
                                 </div>
                                 
                              </div>
                            
                           </div>
                           <div class="container-fluid">
                              <div class="row">
                                 <div class="col-md-12">
                                    <div class="willom">
                                      <h1> Agriculture Farm</h1>
                                    </div>
                                 </div>
                              </div>
                           </div>
         </div>
      </div>
      
      <div class="about">
         <div class="container-fluid">
            <div class="row d_flex">
               <div class="col-lg-6 col-md-12">
                  <div class="titlepage text_align_left">
                     <span>About Us</span>
                     <h2>Empowering Farmers with Data</h2>
                     <p>We help farmers grow smarter. Our smart farming database system gives you real-time access to crop, soil, weather, and irrigation data — all in one place. By making data simple and useful, we help you make better decisions, increase yields, and reduce waste. Whether you're managing one field or many, we're here to make your farm more efficient, productive, and sustainable.</p>
                     <a class="read_more" href="about.html">Learn More</a>
                  </div>
               </div>
               <div class="col-lg-6 col-md-12">
                  <div class="row d_flex">
                   <div class="col-md-7">
                     <div class="about_img">
                        <figure><img src="images/about_img.jpg" alt="#"/>
                        </figure>
                     </div>
                   </div>
                   <div class="col-md-5">
                     <div class="about_img">
                        <figure><img src="images/about_img1.jpg" alt="#"/>
                        </figure>
                     </div>
                   </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
    
      <div class="choose">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <div class="titlepage text_align_center">
                     <h2>Why choose us</h2>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-md-3">
                  <div class="point text_align_center">
                     <h3>300+</h3>
                     <span>Regular <br>Customers</span>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="point text_align_center">
                     <h3>30+</h3>
                     <span>Professional <br>Engineering</span>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="point text_align_center">
                     <h3>300+</h3>
                     <span>Points of Sale  <br>Goods</span>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="point text_align_center">
                     <h3>30+</h3>
                     <span>Awards <br>Won</span>
                  </div>
               </div>
            </div>
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