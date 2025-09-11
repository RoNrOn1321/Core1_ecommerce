<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width-device-width, initial-scale=1.0">
    <title>ShopZone E-commerce</title>

    <!-- font cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">

    <!-- custom css file link -->
    <link rel="stylesheet" href="css/style.css">

    <style>
      #miniCart { display: none; }
      #miniCart.active { display: block; }
      .toast {
        position: fixed; bottom: 20px; right: 20px;
        background: #4ade80; color: white; padding: 10px 20px;
        border-radius: 6px; display: none; z-index: 1000;
      }
    </style>
</head>
<body>

<!-- Toast Notification -->
<div id="toast" class="toast">Item added to cart!</div>

<!-- header section -->

<header>

    <input type="checkbox" name="" id="toggler">
    <label for="toggler" class="fas fa-bars"></label>

    <img class="logo" src="images/logo1.png"> <a class="logo">ShopZone<span>.</span></a>
    


    <nav class="navbar">
        <a href="index.php">home</a>
        <a href="#about">about</a>
        <a href="products.php">products</a>
        <a href="#review">review</a>
        <a href="#contact">contact</a>
    </nav>

    <div class="icons">
        <a href="#" class="fas fa-heart"></a>
        <button id="cartIcon" class="fas fa-shopping-cart" style="background:none; border:none; font-size:2.5rem; color:#333; margin-left:1.5rem; cursor:pointer;">
            <span id="cartCount" style="position:absolute; top:-5px; right:-5px; background:#e74c3c; color:white; width:20px; height:20px; border-radius:50%; font-size:12px; display:flex; align-items:center; justify-content:center;">0</span>
        </button>
        <a href="#" class="fas fa-user"></a>
        
        <!-- Mini Cart -->
        <div id="miniCart" style="position:absolute; top:100%; right:0; width:300px; background:white; box-shadow:0 .5rem 1rem rgba(0,0,0,.1); border-radius:.5rem; padding:2rem; z-index:1000;">
            <h3 style="font-size:2rem; margin-bottom:1rem;">Cart Items</h3>
            <div id="miniCartItems" style="max-height:200px; overflow-y:auto; margin-bottom:1rem;"></div>
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:bold; font-size:1.5rem;">Total: $<span id="miniCartTotal">0.00</span></span>
                <button id="miniCheckoutBtn" class="btn" style="padding:.5rem 1rem; font-size:1.2rem;">Checkout</button>
            </div>
        </div>
    </div>

</header>

<!-- header section ends -->

<!-- home section starts -->

<section class="home" id="home">

    <div class="content">
        <h3>Shop Smart</h3>
        <span> Shop Online</span>
        <p>Discover our wide range of quality products at unbeatable prices. From electronics to home goods - find everything you need in one convenient place with fast shipping and secure payments.</p>
        <a href="#products" class="btn">shop now</a>
    </div>


</section>

<!-- home section ends -->

<!-- products section starts -->

<section class="products" id="products">

    <h1 class="heading"> Latest <span>Products</span> </h1>

    <div class="box-container">

        <div class="box">
            <span class="discount">-10%</span>
            <div class="image">
                <img src="images/img-1.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Wireless Headphones</h3>
                <div class="price">$89.99 <span>$99.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-15%</span>
            <div class="image">
                <img src="images/img-2.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Smart Watch</h3>
                <div class="price">$199.99 <span>$249.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-5%</span>
            <div class="image">
                <img src="images/img-3.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Bluetooth Speaker</h3>
                <div class="price">$49.99 <span>$59.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-20%</span>
            <div class="image">
                <img src="images/img-4.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Laptop Stand</h3>
                <div class="price">$29.99 <span>$39.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-25%</span>
            <div class="image">
                <img src="images/img-1.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Gaming Mouse</h3>
                <div class="price">$39.99 <span>$54.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-30%</span>
            <div class="image">
                <img src="images/img-2.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Tablet Stand</h3>
                <div class="price">$19.99 <span>$29.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-12%</span>
            <div class="image">
                <img src="images/img-3.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>Wireless Charger</h3>
                <div class="price">$24.99 <span>$28.99</span></div>
            </div>
        </div>

        <div class="box">
            <span class="discount">-18%</span>
            <div class="image">
                <img src="images/img-4.jpg" alt="">
                <div class="icons">
                    <a href="#" class="fas fa-heart"></a>
                    <a href="#" class="cart-btn fas fa-shopping-cart"></a>
                    <a href="#" class="fas fa-eye"></a>
                </div>
            </div>
            <div class="content">
                <h3>USB Hub</h3>
                <div class="price">$34.99 <span>$42.99</span></div>
            </div>
        </div>

    </div>

</section>

<!-- products section ends -->


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

<script>
let cart = JSON.parse(localStorage.getItem('cart')) || [];
const toast = document.getElementById('toast');

const cartBtn = document.getElementById('cartIcon');
const miniCart = document.getElementById('miniCart');
const miniCartItems = document.getElementById('miniCartItems');
const miniCartTotal = document.getElementById('miniCartTotal');
const miniCheckoutBtn = document.getElementById('miniCheckoutBtn');

const cartModal = document.getElementById('cartModal');
const closeCart = document.getElementById('closeCart');
const cartItemsContainer = document.getElementById('cartItems');
const cartTotalEl = document.getElementById('cartTotal');
const checkoutBtn = document.getElementById('checkoutBtn');
const cartCount = document.getElementById('cartCount');

function saveCart(){
  localStorage.setItem('cart', JSON.stringify(cart));
  updateCartBadge();
  renderMiniCart();
}

function showToast(message){
  toast.textContent = message;
  toast.style.display = 'block';
  setTimeout(()=>{ toast.style.display='none'; },1500);
}

function updateCartBadge(){
  const totalQty = cart.reduce((acc,item)=>acc+item.qty,0);
  cartCount.textContent = totalQty;
}

function renderCart(){
  cartItemsContainer.innerHTML='';
  let total=0;
  cart.forEach((item,idx)=>{
    total+=item.price*item.qty;
    const div = document.createElement('div');
    div.className="flex justify-between items-center";
    div.innerHTML=`
      <div>
        <h4 class="font-bold">${item.name}</h4>
        <p>$${item.price.toFixed(2)} x ${item.qty}</p>
      </div>
      <div class="flex gap-2">
        <button onclick="updateQty(${idx},-1)" class="px-2 bg-gray-200 rounded">-</button>
        <button onclick="updateQty(${idx},1)" class="px-2 bg-gray-200 rounded">+</button>
        <button onclick="removeItem(${idx})" class="px-2 bg-red-500 text-white rounded">x</button>
      </div>
    `;
    cartItemsContainer.appendChild(div);
  });
  cartTotalEl.textContent=total.toFixed(2);
  saveCart();
}

function renderMiniCart(){
  miniCartItems.innerHTML='';
  let total=0;
  cart.forEach((item,idx)=>{
    total+=item.price*item.qty;
    const div=document.createElement('div');
    div.className="flex justify-between items-center";
    div.innerHTML=`
      <span>${item.name} x ${item.qty}</span>
      <div class="flex gap-1 items-center">
        <span>$${(item.price*item.qty).toFixed(2)}</span>
        <button onclick="removeItem(${idx})" class="text-red-500 font-bold">x</button>
      </div>
    `;
    miniCartItems.appendChild(div);
  });
  miniCartTotal.textContent=total.toFixed(2);
}

function updateQty(index,change){
  cart[index].qty+=change;
  if(cart[index].qty<=0) removeItem(index);
  renderCart();
}

function removeItem(index){
  cart.splice(index,1);
  renderCart();
}

document.querySelectorAll('.cart-btn').forEach(btn=>{
  btn.addEventListener('click',e=>{
    e.preventDefault();
    const box=btn.closest('.box');
    const name=box.querySelector('h3').innerText;
    const price=parseFloat(box.querySelector('.price').innerText.replace('$','').split(' ')[0]);
    const existing=cart.find(item=>item.name===name);
    if(existing){ existing.qty+=1; } 
    else { cart.push({name,price,qty:1}); }
    renderCart();
    showToast(`${name} added to cart!`);
  });
});

cartBtn.addEventListener('click',()=>{ miniCart.classList.toggle('active'); });
checkoutBtn.addEventListener('click',()=>{
  if(cart.length===0){ showToast("Cart is empty!"); return; }
  const total=cart.reduce((acc,item)=>acc+item.price*item.qty,0);
  showToast(`Thank you! Total: $${total.toFixed(2)}`);
  cart=[];
  renderCart();
  cartModal.classList.remove('active');
});
miniCheckoutBtn.addEventListener('click',()=>{
  if(cart.length===0){ showToast("Cart is empty!"); return; }
  const total=cart.reduce((acc,item)=>acc+item.price*item.qty,0);
  showToast(`Thank you! Total: $${total.toFixed(2)}`);
  cart=[];
  renderCart();
  miniCart.classList.remove('active');
});
closeCart.addEventListener('click',()=>cartModal.classList.remove('active'));

renderCart();
</script>

</body>
</html> 