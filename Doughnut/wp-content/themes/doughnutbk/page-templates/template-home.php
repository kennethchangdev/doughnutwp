<?php
/**
 * Template Name: Template Home
 *
 * Template for displaying a page without sidebar even if a sidebar widget is published.
 *
 * @package UnderStrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
$container = get_theme_mod( 'understrap_container_type' );

if ( is_front_page() ) {
	get_template_part( 'global-templates/hero' );
}
?>
<div class="" id="full-width-page-wrapper">

<div class="" id="content">

  <div class="row">

    <div class="content-area" id="primary">

      <main class="site-main" id="main" role="main">

        <!-- EDIT CAROUSEL ON DEPLOYMENT AND TESTING -->
        <div id="myCarousel" class="carousel slide banner-slide" data-ride="carousel">
                <ol class="carousel-indicators">
                  <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                  <li data-target="#myCarousel" data-slide-to="1"></li>
                  <li data-target="#myCarousel" data-slide-to="2"></li>
                </ol>
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img class="first-slide" src="/wp-content/uploads/2020/08/20200817_ROGUE_Website_HomeCarousel_Banner-1.jpg" alt="First slide">

                    <div class="container">
                      <div class="carousel-caption">
                        <h2>Artistry gone Crazy</h2>
                        <h4>Artisanal doughnuts, anytime, anywhere</h4>
                        <button class="btn btn-default order-now-btn">ORDER NOW</button>
                      </div>
                    </div>

                  </div>
                  <div class="carousel-item">
                    <img class="second-slide" src="/wp-content/uploads/2020/08/20200817_ROGUE_Website_HomeCarousel_Banner-2.jpg" alt="Second slide">

                    <div class="container">
                      <div class="carousel-caption">
                        <h2>Freshness gone Limitless</h2>
                        <h4>Handcrafted from scratch, delivered fresh each day</h4>
                        <button class="btn btn-default order-now-btn">ORDER NOW</button>
                      </div>
                    </div>
                  </div>
                  <div class="carousel-item">
                    <img class="third-slide" src="/wp-content/uploads/2020/08/20200817_ROGUE_Website_HomeCarousel_Banner-3.jpg" alt="Third slide">

                    <div class="container">
                      <div class="carousel-caption">
                        <h2>Doughnuts gone Rogue</h2>
                        <h4>Delicately made for those who dare</h4>
                        <button class="btn btn-default order-now-btn">ORDER NOW</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            <!--<div id="rogue-difference-section" class="container-fluid box--shoutout"  name="rogue-difference">
              <div class="row">
              <div class="col-xl-6 left-box">
                <p class="first-p">To go rogue is to know the rules and break<br/>
                  them - to unleash the edge in every<br/> ingredient; to respect the long meticulous process, without shortcuts or cutting corners; <br/> to take pride in the hand-crafted.</p>

                  <p>Each day, we champion local and<br/>responsibly-sourced ingredients to create doughnuts for those who dare.</p>

                  <p>Our dough, glazes, and fillings are made from scratch, delicately crafted in small batches, resulting in the freshest, most flavorful interpretations of doughnuts ever.</p>

                  <p>These are more than your usual doughnuts.<br/>These are doughnuts gone rogue.</p>
              </div>
              <div class="col-xl-6 right-box">
                <div class="right-box-content"></div>
              </div>
            </div>
          </div>-->

          <div class="container-fluid box--shoutout">
                <div class="row">
                  <div class="col-md-6">
                    <div class="paragraph">
                      <p>To go rogue is to know the rules and break 
                      them - to unleash the edge in every 
                      ingredient; to respect the long meticulous 
                      process, without shortcuts or cutting corners;
                      to take pride in the hand-crafted.</p>
                    </div>

                    <div class="paragraph">
                      <p>Each day, we champion local and 
                      responsibly-sourced ingredients to create 
                      doughnuts for those who dare. </p>
                    </div>

                    <div class="paragraph">
                      <p>Our dough, glazes, and fillings are made from 
                      scratch, delicately crafted in small batches, 
                      resulting in the freshest, most flavorful 
                      interpretations of doughnuts ever. </p>
                    </div>

                    <div class="paragraph">
                      <p>These are more than your usual doughnuts. 
                      These are doughnuts gone rogue.</p>
                    </div>
                  </div>

                  <div class="col-md-6">
                  </div>
                </div>
              </div>

          <div class="container-fluid">
                <!-- Product Carousel -->
                <div id="myCarouselFlavors" class="carousel slide products-slide" data-ride="carousel">
                <div class="carousel-inner">

                  <div class="carousel-item active">
                    <div class="row">
                      <div class="col-md-4">
                        <!-- <a class="carousel-grid" href="#"> -->
                        <img src="/wp-content/uploads/2020/08/product-1.png" alt="">
                        <div class="carousel-caption">
                          <h3>Patis Honey Glazed</h3>

                          <div class="hover-product--description">
                            <p>Our signature sourdough doughnut glazed in patis and honey, perfectly sweet and salty.</p>
                          </div>
                        </div>      
                        <!-- </a> -->
                      </div>

                      <div class="col-md-4">
                        <a class="carousel-grid" href="#">
                        <img src="/wp-content/uploads/2020/08/SouthCotabato-scaled.jpg" alt="">
                        <div class="carousel-caption">
                            <h3>60% South Cotabato with Cacao Nibs</h3>

                            <div class="hover-product--description">
                            <p>Our signature sourdough doughnut dipped in  dark chocolate from South Cotabato sprinkled with sea salt and cacao nibs.</p>
                          </div>
                        </div>
                        </a>
                      </div>

                      <div class="col-md-4">
                        <a class="carousel-grid" href="#">
                        <img src="/wp-content/uploads/2020/08/CalamansiGlazed-scaled.jpg" alt="">
                        <div class="carousel-caption">
                            <h3>Calamansi Glazed</h3>

                            <div class="hover-product--description">
                            <p>Our signature sourdough doughnut with sweet and tangy fresh Calamansi glaze.</p>
                          </div>
                        </div>
                        </a>
                      </div>
                    </div>
                    <!-- end of row -->
                  </div>


                  <div class="carousel-item ">
                    <div class="row">
                      <div class="col-md-4">
                        <!-- <a class="carousel-grid" href="#"> -->
                        <img src="/wp-content/uploads/2020/08/LemonSaltMeringue-scaled.jpg" alt="">
                        <div class="carousel-caption">
                          <h3>Lemon Salt Meringue</h3>

                          <div class="hover-product--description">
                            <p>Our signature sourdough doughnut filled with fresh lemon curd topped with torched meringue.</p>
                          </div>
                        </div>      
                        <!-- </a> -->
                      </div>

                      <div class="col-md-4">
                        <a class="carousel-grid" href="#">
                        <img src="/wp-content/uploads/2020/08/product-5.png" alt="">
                        <div class="carousel-caption">
                            <h3>Ube Cheese</h3>

                            <div class="hover-product--description">
                            <p>Our signature sourdough doughnut filled with ube halaya, dipped in ube glaze, and topped with tasty parmesan cheese.</p>
                          </div>
                        </div>
                        </a>
                      </div>

                      <div class="col-md-4">
                        <a class="carousel-grid" href="#">
                        <img src="/wp-content/uploads/2020/08/ChocoHazelButternut-scaled.jpg" alt="">
                        <div class="carousel-caption">
                            <h3>Choco Hazel-Butternut</h3>

                            <div class="hover-product--description">
                            <p>Our signature sourdough doughnut stuffed with hazelnut cream and topped with peanut crumble.</p>
                          </div>
                        </div>
                        </a>
                      </div>
                    </div>
                    <!-- end of row -->
                  </div>

                </div>

                <a class="carousel-control-prev" href="#myCarouselFlavors" role="button" data-slide="prev">
                  <span class="carousel-control-prev-icon" style="height: 70px; width: 70px;" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>

                <a class="carousel-control-next" href="#myCarouselFlavors" role="button" data-slide="next">
                  <span class="carousel-control-next-icon" style="height: 70px; width: 70px;" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>
              </div>

          </div>

          <div class="container-fluid shop-doughnuts" id="doughnut-shop-section" name="doughnut-shop">
          <div class="container shop-section">
            <div class="row-fluid shop-heading-row">
                <h3 class="shop-heading">Shop</h1>
                <hr class="shop-hr-1">
                <h5>Choose Box of Deal</h5>
            </div>

            <div class="row">
                <div class="col-sm-6">
                  <div class="card text-center">
                    <img id="box-of-3" class="card-img-top box-of-3-inactive mx-auto" src="/wp-content/uploads/2020/08/Capture-One-Catalog0301.png">
                    <div class="card-body">
                      <h4 class="card-title">Box of 3</h4>
                      <p class="card-text">3 pcs. of Doughnuts/Box</p>
                      <div class="boxof3-inactive"><input type="number" value="0" min="0" max="100" step="1" class="input-boxof-3" disabled></div>
                    </div>
                  </div>
                </div>

                <div class="col-sm-6">
                  <div class="card text-center">
                    <img id="box-of-6" class="card-img-top box-of-6-inactive mx-auto" src="/wp-content/uploads/2020/08/Capture-One-Catalog0288.png">
                    <div class="card-body">
                      <h4 class="card-title">Box of 6</h4>
                      <p class="card-text">6 pcs. of Doughnuts/Box</p>
                      <input type="number" value="0" min="0" max="100" step="1" class="input-boxof-6" disabled>
                    </div>
                  </div>
                </div>
            </div>


            <div class="row-fluid shop-flavor-heading">
                <hr class="shop-hr-flavor">
                <h5>Choose <span class="doughnut-number">your</span> doughnuts</h5>
            </div>

            <div class="row shop-flavor-row1">
              <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI = 77; //Product ID
                    $pro = new WC_Product($product_DI);
                    echo '<img class="card-img-top cart-prod-img flavor-patis patis-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-1.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro->get_title();'</p>';
                    echo '<p class="card-text" id="patis-price">Php '.$pro->get_price();'';
                    echo '</p>';
                    echo '<input type="number" value="0" min="0" max="100" step="1" class="qty-patis" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="patis-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI = 211; //Product ID
                    $pro = new WC_Product($product_DI);
                    echo '<img class="card-img-top cart-prod-img flavor-cotabato cotabato-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-2.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro->get_title();'</p>';
                    echo '<p class="card-text">Php '.$pro->get_price();'';
                    echo '</p>';
                    echo '<input type="number" value="0" min="0" max="100" step="1" class="qty-cotabato" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="cotabato-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI = 81; //Product ID
                    $pro = new WC_Product($product_DI);
                    echo '<img class="card-img-top cart-prod-img flavor-calamansi calamansi-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-3.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro->get_title();'</p>';
                    echo '<p class="card-text">Php '.$pro->get_price();'';
                    echo '</p>';
                    echo '<input id="qty-patis" type="number" value="0" min="0" max="100" step="1" class="qty-calamansi" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="calamansi-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row  shop-flavor-row2">
            <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI = 83; //Product ID
                    $pro = new WC_Product($product_DI);
                    echo '<img class="card-img-top cart-prod-img flavor-lemon-meringue lemon-meringue-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-4.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro->get_title();'</p>';
                    echo '<p class="card-text">Php '.$pro->get_price();'';
                    echo '</p>';
                    echo '<input id="qty-lemon-meringue" type="number" value="0" min="0" max="100" step="1" class="qty-lemon-meringue" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="meringue-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI5 = 85; //Product ID
                    $pro5 = new WC_Product($product_DI5);
                    echo '<img class="card-img-top cart-prod-img flavor-ube-cheese ube-cheese-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-5.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro5->get_title();'</p>';
                    echo '<p class="card-text">Php '.$pro5->get_price();'';
                    echo '</p>';
                    echo '<input id="qty-ube-cheese" type="number" value="0" min="0" max="100" step="1" class="qty-ube-cheese" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="ube-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>

              <div class="col-sm-4">
                <div class="card">
                <?php 
                    $product_DI = 87; //Product ID
                    $pro = new WC_Product($product_DI);
                    echo '<img class="card-img-top cart-prod-img flavor-hazel-butternut hazel-butternut-inactive" src="http://doughnutwoo.com/wp-content/uploads/2020/08/doughnut-6.png"/>';
                    echo '<div class="card-body">';
                    echo '<p class="card-title">'.$pro->get_title();'</p>';
                    echo '<p class="card-text">Php '.$pro->get_price();'';
                    echo '</p>';
                    echo '<input id="qty-hazel-butternut" type="number" value="0" min="0" max="100" step="1" class="qty-hazel-butternut" name="flavor-qty" disabled>';
                    echo '<input type="text" value="" class="butternut-hidden-url" hidden>';
                    
                ?>
                  </div>
                </div>
              </div>
            </div>

            <div class="row justify-content-center" style="margin-bottom: 20px;">
                  <div class="col-md-6">
                      <button type="button" id="AddToCartBtn" class="btn btn-warning btn-block" disabled><i class="fas fa-shopping-cart"  id="AddToCart-Icon" style="padding-right: 10px;"></i><span id="AddToCart-Span">ADD TO CART</span></button>
                  </div>
                  <div class="col-md-6">
                      <button type="button" id="BuyNowBtn" class="btn btn-warning btn-block" disabled><span id="BuyNow-Span">BUY NOW</span></button>
                  </div>
            </div>
          </div>
          </div>

              <div class="container-fluid footer">
                <div class="row">
                      <div class="col-md-4 footer-location">
                        <a href="#" class="btn btn-default btn-block">
                          <i class="fas fa-map-marker-alt"></i>
                          <h4>Store Locator</h4>
                        </a>
                      </div>

                      <div class="col-md-4 footer-facebook">
                        <a href="#" class="btn btn-default btn-block">
                        <i class="fab fa-facebook-f"></i>
                          <h4>roguedoughnuts</h4>
                        </a>
                      </div>

                      <div class="col-md-4 footer-ig">
                        <a href="#" class="btn btn-default btn-block">
                          <i class="fab fa-instagram"></i>
                          <h4>doughnutsgonerogue</h4>
                        </a>
                      </div>
                  </div>
              </div>

        <input type="hidden" class="selected-flavors-txt" style="display: none;" />


        </main><!-- #main -->

      </div><!-- #primary -->

    </div><!-- .row end -->

  </div><!-- #content -->

</div><!-- #full-width-page-wrapper -->
<?php

/*if(isset($_POST['selected_flavors']) && $_POST['selected_flavors'] != ""){
  $Box_Deal_Field = sanitize_text_field($_POST['selected_box_deal']);
  $Selected_Flavors_Field = sanitize_text_field($_POST['selected_flavors']);
  $Box_Deal_Qty_Field = sanitize_text_field($_POST['box_deal_qty']);
 
  add_to_cart_custom_me($Box_Deal_Field, $Box_Deal_Qty_Field, $Selected_Flavors_Field);

  //var_dump($_POST['selected_flavors']);
}*/

/*function add_to_cart_custom_me($box_id, $deal_quantity, $selected_flavors)  
{
  WC()->cart->add_to_cart($box_id, $deal_quantity, 0, array(), array('doughnut_flavors' => array('Flavors'=>$selected_flavors)));
}*/

//Function to add to cart. (work around)
//See doughnut_shop.js (/js/doughnut_shop.js/)
//doughnut_shop.js file contains the AJAX post request.
//Doon kumukuha ng data etong work around na to. 
function test_add_to_cart($test_data_input, $test_qty_input, $chosen_box_deal){
  WC()->cart->add_to_cart($chosen_box_deal, $test_qty_input, 0, array(), array('doughnut_flavors' => array('Flavors'=>$test_data_input)));
}

if(isset($_POST['SelectedFlavors']) && isset($_POST['testqty']) && isset($_POST['chosenboxdeal'])){
  $selected_flavors = sanitize_text_field($_POST['SelectedFlavors']);
  test_add_to_cart($selected_flavors, $_POST['testqty'], $_POST['chosenboxdeal']);
}

get_footer();