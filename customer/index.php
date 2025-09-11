<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumino E-commerce - Home</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/navbar.php'; ?>

<script src="assets/js/customer-api.js"></script>

<!-- home section starts -->

<section class="home" id="home">

    <div class="content">
        <h3>Lumino</h3>
        <span> Shop Online</span>
        <p>Discover amazing products at unbeatable prices. From electronics to fashion, home goods to sports equipment - find everything you need in one convenient place with fast shipping and secure payments.</p>
        <a href="products.php" class="btn">shop now</a>
    </div>


</section>

<!-- home section ends -->

<!-- about section starts -->

<section class="about" id="about">

    <h1 class="heading"> <span> about </span> us </h1>

    <div class="row">

        <div class="video-container">
            <video src="images/about-vid.mp4" loop autoplay muted></video>
            <h3>Best online shopping experience</h3>
        </div>

        <div class="content">
            <h3>why choose us?</h3>
            <p>We offer a vast selection of quality products from trusted brands at competitive prices. Our user-friendly platform makes shopping easy and secure with multiple payment options and reliable customer support.</p>
            <p>With fast shipping, hassle-free returns, and 24/7 customer service, we're committed to providing you with the best online shopping experience. Join millions of satisfied customers who trust us for their shopping needs.</p>
            <a href="#" class="btn">Learn More</a>
        </div>

    </div>

</section>

<!-- about section ends -->

<!-- icon section starts -->

<section class="icons-container">

    <div class="icons">
        <img src="images/icon-1.png" alt="">
        <div class="info">
            <h3>Free Delivery</h3>
            <span>on all orders</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-2.png" alt="">
        <div class="info">
            <h3>10 days returns</h3>
            <span>moneyback guarantee</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-3.png" alt="">
        <div class="info">
            <h3>Offer & Gifts</h3>
            <span>on all orders</span>
        </div>
    </div>

    <div class="icons">
        <img src="images/icon-4.png" alt="">
        <div class="info">
            <h3>Secured Payment</h3>
            <span>Protected by Paypal</span>
        </div>
    </div>

</section>

<!-- icon section ends -->


<!-- review section starts -->

<section class="review" id="review">

<h1 class="heading"> Customer's <span>review</span> </h1>

<div class="box-container">

    <div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-1.png" alt="">
            <div class="user-info">
            <h3>Vinny Hong</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-2.png" alt="">
            <div class="user-info">
            <h3>Sanzu Haruchiyo</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-3.png" alt="">
            <div class="user-info">
            <h3>Leon Winston</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>

<div class="box">
        <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
        </div>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto esse nemo omnis! Nesciunt animi ea vitae recusandae incidunt natus? Vel mollitia excepturi harum placeat dolorem vero non incidunt ut. Deserunt.</p>
        <div class="user">
            <img src="images/pic-4.png" alt="">
            <div class="user-info">
            <h3>Eiser Grayon</h3>
            <span>Happy Customer</span>
        </div>
    </div>
    <span class="fas fa-quote-right"></span>
</div>


</section>

<!-- review section ends -->

<!-- contact section starts -->

<section class="contact" id="contact">

    <h1 class="heading"> <span> Contact </span> Us </h1>

    <div class="row">

        <form action="">
            <input type="text" placeholder="name" class="box">
            <input type="email" placeholder="email" class="box">
            <input type="number" placeholder="number" class="box">
            <textarea name="" class="box" placeholder="message" id="" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" class="btn">
        </form>

        <div class="image">
            <img src="images/contact-img.png" alt="">
        </div>

    </div>
</section>

<!-- contact section ends -->

<!-- footer section starts -->

<section class="footer">

<div class="box-container">

    <div class="box">
        <h3>Quick Links</h3>
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Products</a>
        <a href="#">Review</a>
        <a href="#">Contact</a>
    </div>

    <div class="box">
        <h3>Extra Links</h3>
        <a href="#">My Account</a>
        <a href="#">My Order</a>
        <a href="#">My Favorite</a>
    </div>

   

    <div class="box">
        <h3>Contact Info</h3>
        <a href="#">+639-123-45678</a>
        <a href="#">example@gmail.com</a>
        <a href="#">Manila, Philippines</a>
    </div>
</div>

<div class="credit"> Created By <span> ShopZone Team</span> | All rights reserved</div>

</section>


<!-- footer section ends -->

</body>
</html>

