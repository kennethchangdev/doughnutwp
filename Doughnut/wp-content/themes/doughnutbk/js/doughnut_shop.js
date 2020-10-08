//The JQuery file for the shop
//Needs to be revised, since client requested to control stock management. 
//Validation for the shop goes here and needs to fixed as well. 
jQuery(document).ready
jQuery(document).ready(function(){
    //jQuery Bootstrap input spinner, styles all of the quantity input box
    jQuery("input[type='number']").inputSpinner();

    //Flavor isActiveflags
    var isActivePatis = false; 
    var isActiveCotabato = false;
    var isActiveCalamansi = false;
    var isActiveLemonMeringue = false;
    var isActiveUbeCheese= false;
    var isActiveHazelButternut = false;

    jQuery('.nav-link').css('cursor', 'pointer');
    jQuery('.nav-link').removeAttr('href');

    //Click event listener for the button on the banner carousel
    jQuery('.order-now-btn').click(function(){
        document.getElementById("doughnut-shop-section").scrollIntoView({behavior: 'smooth'});
    });


    //Click event listners for nav-bar Page Jumping 
    jQuery('#menu-item-7 a:nth-child(1)').click(function(){
        location.href = 'index.php';
    });

    jQuery('#menu-item-9 a:nth-child(1)').click(function(){
        document.getElementById("rogue-difference-section").scrollIntoView({behavior: 'smooth'});
    });

    jQuery('#menu-item-10 a:nth-child(1)').click(function(){
        document.getElementById("myCarouselFlavors").scrollIntoView({behavior: 'smooth'});
    });

    jQuery('#menu-item-11 a:nth-child(1)').click(function(){
        document.getElementById("doughnut-shop-section").scrollIntoView({behavior: 'smooth'});
    });

    jQuery('#menu-item-12 a:nth-child(1)').click(function(){
        document.getElementById("rogue-social-media").scrollIntoView({behavior: 'smooth'});
    });

    jQuery('#box-of-3').click(function(){

        resetFields();
        isActivePatis = false; 
        isActiveCotabato = false;
        isActiveCalamansi = false;
        isActiveLemonMeringue = false;
        isActiveUbeCheese= false;
        isActiveHazelButternut = false;

        jQuery('#box-of-3').attr('class', 'card-img-top box-of-3-active mx-auto');
        jQuery('.input-boxof-3').removeAttr('disabled');

        jQuery('.input-boxof-6').attr('disabled', true);
        jQuery('.input-boxof-6').val(0);
        jQuery('#box-of-6').attr('class', 'card-img-top box-of-6-inactive mx-auto');
    });

    jQuery('#box-of-6').click(function(){

        resetFields();
        isActivePatis = false; 
        isActiveCotabato = false;
        isActiveCalamansi = false;
        isActiveLemonMeringue = false;
        isActiveUbeCheese= false;
        isActiveHazelButternut = false;

        jQuery('.input-boxof-3').attr('disabled', true);
        jQuery('#box-of-3').attr('class', 'card-img-top box-of-3-inactive mx-auto');
        jQuery('.input-boxof-3').val(0);
        
        jQuery('#box-of-6').attr('class', 'card-img-top box-of-6-active mx-auto');
        jQuery('.input-boxof-6').removeAttr('disabled');
    });

    //Click event listener for flavors on the shop section

    //Patis
    jQuery('.flavor-patis').click(function(){
        if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
        {
            if(jQuery('.box-of-3-active').length > 0)
            {
                var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfThreeMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfSixMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }

            if(isActivePatis == false)
            {
                jQuery('.flavor-patis').attr('class', 'card-img-top flavor-patis patis-active');
                jQuery('.qty-patis').attr('disabled', false);
                jQuery('.qty-patis').val(1);
                jQuery('.patis-hidden-url').val("Patis Sugar Raised (1 pcs.)");
                jQuery('#patis-price').val('#patis-price');
                isActivePatis = true;
            }
            else
            {
                isActivePatis = false; 
                jQuery('.flavor-patis').attr('class', 'card-img-top flavor-patis patis-inactive');
                jQuery('.qty-patis').attr('disabled', true);
                jQuery('.qty-patis').val(0);
                jQuery('.patis-hidden-url').val("");
            }
        }
        else
        {
            alert("Please choose a box deal first.");
        }
    });

    //South Cotabato Choco
    jQuery('.flavor-cotabato').click(function(){
        if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
        {
            if(jQuery('.box-of-3-active').length > 0)
            {
                var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                var countCurrentQty = findTotalQty();
                
                if(countCurrentQty >= boxOfThreeMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfSixMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }

            if(isActiveCotabato == false)
            {
                jQuery('.flavor-cotabato').attr('class', 'card-img-top flavor-cotabato cotabato-active');
                jQuery('.qty-cotabato').attr('disabled', false);
                jQuery('.qty-cotabato').val(1);
                jQuery('.cotabato-hidden-url').val("60% South Cotabato w/ Cacao Nibs (1 pcs.)");
                isActiveCotabato = true;
            }
            else
            {
                isActiveCotabato = false; 
                jQuery('.flavor-cotabato').attr('class', 'card-img-top flavor-cotabato cotabato-inactive');
                jQuery('.qty-cotabato').attr('disabled', true);
                jQuery('.qty-cotabato').val(0);
                jQuery('.cotabato-hidden-url').val("");
            }
        }
        else
        {
            alert("Please choose a box deal first.");
        }
    });

    //Calamansi Glazed
    jQuery('.flavor-calamansi').click(function(){
        if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
        {
            if(jQuery('.box-of-3-active').length > 0)
            {
                var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfThreeMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfSixMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }

            if(isActiveCalamansi== false)
            {
                jQuery('.flavor-calamansi').attr('class', 'card-img-top flavor-calamansi calamansi-active');
                jQuery('.qty-calamansi').attr('disabled', false);
                jQuery('.qty-calamansi').val(1);
                jQuery('.calamansi-hidden-url').val("Calamansi Glazed (1 pcs.)");
                isActiveCalamansi = true;
            }
            else
            {
                isActiveCalamansi = false; 
                jQuery('.flavor-calamansi').attr('class', 'card-img-top flavor-calamansi calamansi-inactive');
                jQuery('.qty-calamansi').attr('disabled', true);
                jQuery('.qty-calamansi').val(0);
                jQuery('.calamansi-hidden-url').val("");
            }
        }
        else
        {
            alert("Please choose a box deal first.");
        }
    });

    //Lemon Salt Meringue
    jQuery('.flavor-lemon-meringue').click(function(){
        if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
        {
            if(jQuery('.box-of-3-active').length > 0)
            {
                var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfThreeMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfSixMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }

            if(isActiveLemonMeringue == false)
            {
                jQuery('.flavor-lemon-meringue').attr('class', 'card-img-top flavor-lemon-meringue lemon-meringue-active');
                jQuery('.qty-lemon-meringue').attr('disabled', false);
                jQuery('.qty-lemon-meringue').val(1);
                jQuery('.meringue-hidden-url').val("Lemon Salt Meringue (1 pcs.)");
                isActiveLemonMeringue = true;
            }
            else
            {
                isActiveLemonMeringue = false; 
                jQuery('.flavor-lemon-meringue').attr('class', 'card-img-top flavor-lemon-meringue lemon-meringue-inactive');
                jQuery('.qty-lemon-meringue').attr('disabled', true);
                jQuery('.qty-lemon-meringue').val(0);
                jQuery('.meringue-hidden-url').val("");
            }
        }
        else
        {
            alert("Please choose a box deal first.");
        }
    });

        //Ube Cheese
        jQuery('.flavor-ube-cheese').click(function(){
            if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
            {
                if(jQuery('.box-of-3-active').length > 0)
                {
                    var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                    var countCurrentQty = findTotalQty();
    
                    if(countCurrentQty >= boxOfThreeMax)
                    {
                        alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                        return;
                    }
                }
                else if(jQuery('.box-of-6-active').length > 0)
                {
                    var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                    var countCurrentQty = findTotalQty();
    
                    if(countCurrentQty >= boxOfSixMax)
                    {
                        alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                        return;
                    }
                }

                if(isActiveUbeCheese== false)
                {
                    jQuery('.flavor-ube-cheese').attr('class', 'card-img-top flavor-ube-cheese ube-cheese-active');
                    jQuery('.qty-ube-cheese').attr('disabled', false);
                    jQuery('.qty-ube-cheese').val(1);
                    jQuery('.ube-hidden-url').val("Ube-Cheese (1 pcs)");
                    isActiveUbeCheese = true;
                }
                else
                {
                    isActiveUbeCheese = false; 
                    jQuery('.flavor-ube-cheese').attr('class', 'card-img-top flavor-ube-cheese ube-cheese-inactive');
                    jQuery('.qty-ube-cheese').attr('disabled', true);
                    jQuery('.qty-ube-cheese').val(0);
                    jQuery('.ube-hidden-url').val("");
                }
            }
            else
            {
                alert("Please choose a box deal first.");
            }
        });


    //Choco Hazel Butternut
    jQuery('.flavor-hazel-butternut').click(function(){
        if(jQuery('.box-of-3-active').length > 0 || jQuery('.box-of-6-active').length > 0)
        {
            if(jQuery('.box-of-3-active').length > 0)
            {
                var boxOfThreeMax =  jQuery('.input-boxof-3').val() * 3;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfThreeMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                var boxOfSixMax =  jQuery('.input-boxof-6').val() * 6;
                var countCurrentQty = findTotalQty();

                if(countCurrentQty >= boxOfSixMax)
                {
                    alert('You have exceeded the maximum quantity possible for your chosen box deal!');
                    return;
                }
            }

            if(isActiveHazelButternut== false)
            {
                jQuery('.flavor-hazel-butternut').attr('class', 'card-img-top flavor-hazel-butternut hazel-butternut-active');
                jQuery('.qty-hazel-butternut').attr('disabled', false);
                jQuery('.qty-hazel-butternut').val(1);
                jQuery('.butternut-hidden-url').val("Choco-Hazel Butternut (1 pcs.)");
                isActiveHazelButternut = true;
            }
            else
            {
                isActiveHazelButternut = false; 
                jQuery('.flavor-hazel-butternut').attr('class', 'card-img-top flavor-hazel-butternut hazel-butternut-inactive');
                jQuery('.qty-hazel-butternut').attr('disabled', true);
                jQuery('.qty-hazel-butternut').val(0);
                jQuery('.butternut-hidden-url').val("");
            }
        }
        else
        {
            alert("Please choose a box deal first.");
        }
    });

    jQuery('.input-boxof-3').on("change paste keyup", function(){
        if(jQuery('.input-boxof-3').val() >= 1)
        {
            var boxOf3_Input = jQuery('.input-boxof-3').val();
            var doughnut_number = calculateMaxFlavorsAllowed(125, boxOf3_Input);
            jQuery('#AddToCartBtn').attr('disabled', false);
            jQuery('#BuyNowBtn').attr('disabled', false);
            jQuery('.doughnut-number').html('<span class = ".doughnut-number">'+doughnut_number+'</span>');
        }
        else
        {
            jQuery('#AddToCartBtn').attr('disabled', true);
            jQuery('#BuyNowBtn').attr('disabled', true);
            jQuery('.doughnut-number').html('<span class = ".doughnut-number">your</span>');
        }
    });

    jQuery('.input-boxof-6').on("change paste keyup", function(){
        if(jQuery('.input-boxof-6').val() >= 1)
        {
            var boxOf6_Input = jQuery('.input-boxof-6').val();
            var doughnut_number = calculateMaxFlavorsAllowed(126, boxOf6_Input);
            jQuery('#AddToCartBtn').attr('disabled', false);
            jQuery('#BuyNowBtn').attr('disabled', false);
            jQuery('.doughnut-number').html('<span class = ".doughnut-number">'+doughnut_number+'</span>');
        }
        else
        {
            jQuery('#AddToCartBtn').attr('disabled', true);
            jQuery('#BuyNowBtn').attr('disabled', true);
            jQuery('.doughnut-number').html('<span class = ".doughnut-number">your</span>');
        }
    });

    jQuery('.qty-patis').on("change paste keyup", function(){
        var patisQty = jQuery('.qty-patis').val();
        var patisAddToCart = " Patis Sugar Raised (";
        var patisLink = patisAddToCart.concat(patisQty, " pcs.)");

        jQuery('.patis-hidden-url').val(patisLink);
    });

    jQuery('.qty-cotabato').on("change paste keyup", function(){
        var cotabatoQty = jQuery('.qty-cotabato').val();
        var cotabatoAddToCart = " 60% South Cotabato w/ Cacao Nibs (";
        var cotabatoLink = cotabatoAddToCart.concat(cotabatoQty, " pcs.)");

        jQuery('.cotabato-hidden-url').val(cotabatoLink);
    });

    jQuery('.qty-calamansi').on("change paste keyup", function(){
        var calamansiQty = jQuery('.qty-calamansi').val();
        var calamansiAddToCart = " Calamansi Glazed (";
        var calamansiLink = calamansiAddToCart.concat(calamansiQty, " pcs.)");

        jQuery('.calamansi-hidden-url').val(calamansiLink);
    });

    jQuery('.qty-lemon-meringue').on("change paste keyup", function(){
        var meringueQty = jQuery('.qty-lemon-meringue').val();
        var meringueAddToCart = " Lemon Salt Meringue (";
        var meringueLink = meringueAddToCart.concat(meringueQty, " pcs.)");

        jQuery('.meringue-hidden-url').val(meringueLink);
    });

    jQuery('.qty-ube-cheese').on("change paste keyup", function(){
        var ubeQty = jQuery('.qty-ube-cheese').val();
        var ubeAddToCart = " Ube Cheese (";
        var ubeLink = ubeAddToCart.concat(ubeQty, " pcs.)");

        jQuery('.ube-hidden-url').val(ubeLink);
    });

    jQuery('.qty-hazel-butternut').on("change paste keyup", function(){
        var butternutQty = jQuery('.qty-hazel-butternut').val();
        var butternutAddToCart = " Choco Hazel-Butternut (";
        var butternutLink = butternutAddToCart.concat(butternutQty, " pcs.)");

        jQuery('.butternut-hidden-url').val(butternutLink);
    });

    jQuery('.selected-flavors-txt').on("change paste keyup", function(){
        console.log(jQuery('.selected-flavors-txt').val());
    });

    jQuery('#AddToCartBtn').click(function(){

        var el = document.getElementById("AddToCartBtn");
        var spanElem = document.getElementById("AddToCart-Span");
        var check_el = document.getElementById("AddToCart-Icon");

        //Disables button and change span element text, gives visual feedback to the user
        el.disabled = true;
        spanElem.innerHTML = "ADDING...";

        var totalQty = findTotalQty();
        var dealID = 0;
        var dealQty = 0;

        if(jQuery('.box-of-3-active').length > 0)
        {
            dealID = 125;
            dealQty = jQuery('.input-boxof-3').val();
        }
        else if(jQuery('.box-of-6-active').length > 0)
        {
            dealID = 126;
            dealQty = jQuery('.input-boxof-6').val();
        }

        var maxFlavors = calculateMaxFlavorsAllowed(dealID, dealQty);
        var currentTotalFlavorQty = findTotalQty();

        //Wait for some seconds to pass to change some attributes of the button, then set the attributes back to their initial values. 
        //This is again purely just for visual feedback, when a customer clicks the add to cart button.
        if(currentTotalFlavorQty > maxFlavors)
        {
            setTimeout(function(){
                check_el.setAttribute('class', "fas fa-times"); 
                spanElem.innerHTML = "ERROR"; 
                el.setAttribute('class', "btn btn-danger btn-block"); }, 1500);

            setTimeout(function(){
                            el.disabled = false; 
                            check_el.setAttribute('class', "fas fa-shopping-cart"); 
                            spanElem.innerHTML = "ADD TO CART"; el.setAttribute('class', "btn btn-warning btn-block"); }, 2500);

            alert('You cannot add any more flavors! Please Try Again.');
        }
        else if(currentTotalFlavorQty <= 0 || currentTotalFlavorQty < maxFlavors)
        {
            setTimeout(function(){
                check_el.setAttribute('class', "fas fa-times"); 
                spanElem.innerHTML = "ERROR"; 
                el.setAttribute('class', "btn btn-danger btn-block"); }, 1500);

            setTimeout(function(){
                            el.disabled = false; 
                            check_el.setAttribute('class', "fas fa-shopping-cart"); 
                            spanElem.innerHTML = "ADD TO CART"; el.setAttribute('class', "btn btn-warning btn-block"); }, 2500);

            alert('Please check the quantity for the flavors you have selected and try again.');

        }
        else if(currentTotalFlavorQty === maxFlavors)
        {
            addToCart();
        }
    });

    jQuery('#BuyNowBtn').click(function(){
        var el = document.getElementById("BuyNowBtn");
        var spanElem = document.getElementById("BuyNow-Span");

        //Disables button and change span element text, gives visual feedback to the user
        el.disabled = true;
        spanElem.innerHTML = "ADDING...";

        var dealID = 0;
        var dealQty = 0;

        if(jQuery('.box-of-3-active').length > 0)
        {
            dealID = 125;
            dealQty = jQuery('.input-boxof-3').val();
        }
        else if(jQuery('.box-of-6-active').length > 0)
        {
            dealID = 126;
            dealQty = jQuery('.input-boxof-6').val();
        }

        var maxFlavors = calculateMaxFlavorsAllowed(dealID, dealQty);
        var currentTotalFlavorQty = findTotalQty();

        //Wait for some seconds to pass to change some attributes of the button, then set the attributes back to their initial values. 
        //This is again purely just for visual feedback, when a customer clicks the add to cart button.
        if(currentTotalFlavorQty > maxFlavors)
        {
            setTimeout(function(){
                spanElem.innerHTML = "ERROR"; 
                el.setAttribute('class', "btn btn-danger btn-block"); }, 1500);

            setTimeout(function(){
                            el.disabled = false; 
                            spanElem.innerHTML = "BUY NOW"; el.setAttribute('class', "btn btn-warning btn-block"); }, 2500);

            alert('You cannot add any more flavors! Please Try Again.');
        }

        else if(currentTotalFlavorQty <= 0 || currentTotalFlavorQty < maxFlavors)
        {
            setTimeout(function(){
                spanElem.innerHTML = "ERROR"; 
                el.setAttribute('class', "btn btn-danger btn-block"); }, 1500);

            setTimeout(function(){
                            el.disabled = false; 
                            spanElem.innerHTML = "BUY NOW"; el.setAttribute('class', "btn btn-warning btn-block"); }, 2500);

            alert('Please check the quantity for the flavors you have selected and try again.');
        }

        else if(currentTotalFlavorQty === maxFlavors)
        {
            var test_wew = constructOrders();
            var boxDealQty = 0;
            var chosenBoxDeal = 0;

            if(jQuery('.box-of-3-active').length > 0)
            {
                chosenBoxDeal = 125;
                boxDealQty = jQuery('.input-boxof-3').val();
            }
            else if(jQuery('.box-of-6-active').length > 0)
            {
                chosenBoxDeal = 126;
                boxDealQty = jQuery('.input-boxof-6').val();
            }
            
            console.log(test_wew);
    
            jQuery.ajax({
                type: 'post',
                url: 'index.php',
                data: { 
                        'SelectedFlavors': test_wew,
                        'testqty' : boxDealQty,
                        'chosenboxdeal' : chosenBoxDeal
                },
                success: function () {
                    console.log("Added To Cart");
                },
                error: function () {
                    alert("error");
                }
            });
    
            resetFields();
            isActivePatis = false; 
            isActiveCotabato = false;
            isActiveCalamansi = false;
            isActiveLemonMeringue = false;
            isActiveUbeCheese= false;
            isActiveHazelButternut = false;

            window.location.href = '/checkout/';
        }
    });

});

function addToCart()
{
    var el = document.getElementById("AddToCartBtn");
    var spanElem = document.getElementById("AddToCart-Span");
    var check_el = document.getElementById("AddToCart-Icon");

    setTimeout(function(){
        check_el.setAttribute('class', "fas fa-check"); 
        spanElem.innerHTML = "SUCCESSFULLY ADDED!"; 
        el.setAttribute('class', "btn btn-success btn-block"); }, 1500);

    setTimeout(function(){
        check_el.setAttribute('class', "fas fa-shopping-cart"); 
        spanElem.innerHTML = "ADD TO CART"; el.setAttribute('class', "btn btn-warning btn-block");}, 2500);

        var test_wew = constructOrders();
        var boxDealQty = 0;
        var chosenBoxDeal = 0;

        if(jQuery('.box-of-3-active').length > 0)
        {
            chosenBoxDeal = 125;
            boxDealQty = jQuery('.input-boxof-3').val();
        }
        else if(jQuery('.box-of-6-active').length > 0)
        {
            chosenBoxDeal = 126;
            boxDealQty = jQuery('.input-boxof-6').val();
        }
        
        console.log(test_wew);

        jQuery.ajax({
            type: 'post',
            url: 'index.php',
            data: { 
                    'SelectedFlavors': test_wew,
                    'testqty' : boxDealQty,
                    'chosenboxdeal' : chosenBoxDeal
            },
            success: function () {
                console.log("Added To Cart");
            },
            error: function () {
                alert("error");
            }
        });

        resetFieldsAll();
        isActivePatis = false; 
        isActiveCotabato = false;
        isActiveCalamansi = false;
        isActiveLemonMeringue = false;
        isActiveUbeCheese= false;
        isActiveHazelButternut = false;
}

function resetFields()
{

    //Patis Reset
    jQuery('.flavor-patis').attr('class', 'card-img-top flavor-patis patis-inactive');
    jQuery('.qty-patis').attr('disabled', true);
    jQuery('.qty-patis').val(0);
    jQuery('.patis-hidden-url').val("");

    //South Cotabato Reset
    jQuery('.flavor-cotabato').attr('class', 'card-img-top flavor-cotabato cotabato-inactive');
    jQuery('.qty-cotabato').attr('disabled', true);
    jQuery('.qty-cotabato').val(0);
    jQuery('.cotabato-hidden-url').val("");

    //Calamansi Reset 
    jQuery('.flavor-calamansi').attr('class', 'card-img-top flavor-calamansi calamansi-inactive');
    jQuery('.qty-calamansi').attr('disabled', true);
    jQuery('.qty-calamansi').val(0);
    jQuery('.calamansi-hidden-url').val("");

    //Meringue Reset
    isActiveLemonMeringue = false; 
    jQuery('.flavor-lemon-meringue').attr('class', 'card-img-top flavor-lemon-meringue lemon-meringue-inactive');
    jQuery('.qty-lemon-meringue').attr('disabled', true);
    jQuery('.qty-lemon-meringue').val(0);
    jQuery('.meringue-hidden-url').val("");

    //Ube Cheese Reset
    jQuery('.flavor-ube-cheese').attr('class', 'card-img-top flavor-ube-cheese ube-cheese-inactive');
    jQuery('.qty-ube-cheese').attr('disabled', true);
    jQuery('.qty-ube-cheese').val(0);
    jQuery('.ube-hidden-url').val("");

    //Butternut Reset
    jQuery('.flavor-hazel-butternut').attr('class', 'card-img-top flavor-hazel-butternut hazel-butternut-inactive');
    jQuery('.qty-hazel-butternut').attr('disabled', true);
    jQuery('.qty-hazel-butternut').val(0);
    jQuery('.butternut-hidden-url').val("");

    jQuery('.doughnut-number').html('<span class = ".doughnut-number">your</span>');
}

function resetFieldsAll(){
        jQuery('.input-boxof-3').attr('disabled', true);
        jQuery('#box-of-3').attr('class', 'card-img-top box-of-3-inactive mx-auto');
        jQuery('.input-boxof-3').val(0);

        jQuery('.input-boxof-6').attr('disabled', true);
        jQuery('.input-boxof-6').val(0);
        jQuery('#box-of-6').attr('class', 'card-img-top box-of-6-inactive mx-auto');

        jQuery('#AddToCartBtn').attr('disabled', true);
        jQuery('#BuyNowBtn').attr('disabled', true);

        //Patis Reset
        jQuery('.flavor-patis').attr('class', 'card-img-top flavor-patis patis-inactive');
        jQuery('.qty-patis').attr('disabled', true);
        jQuery('.qty-patis').val(0);
        jQuery('.patis-hidden-url').val("");
    
        //South Cotabato Reset
        jQuery('.flavor-cotabato').attr('class', 'card-img-top flavor-cotabato cotabato-inactive');
        jQuery('.qty-cotabato').attr('disabled', true);
        jQuery('.qty-cotabato').val(0);
        jQuery('.cotabato-hidden-url').val("");
    
        //Calamansi Reset 
        jQuery('.flavor-calamansi').attr('class', 'card-img-top flavor-calamansi calamansi-inactive');
        jQuery('.qty-calamansi').attr('disabled', true);
        jQuery('.qty-calamansi').val(0);
        jQuery('.calamansi-hidden-url').val("");
    
        //Meringue Reset
        isActiveLemonMeringue = false; 
        jQuery('.flavor-lemon-meringue').attr('class', 'card-img-top flavor-lemon-meringue lemon-meringue-inactive');
        jQuery('.qty-lemon-meringue').attr('disabled', true);
        jQuery('.qty-lemon-meringue').val(0);
        jQuery('.meringue-hidden-url').val("");
    
        //Ube Cheese Reset
        jQuery('.flavor-ube-cheese').attr('class', 'card-img-top flavor-ube-cheese ube-cheese-inactive');
        jQuery('.qty-ube-cheese').attr('disabled', true);
        jQuery('.qty-ube-cheese').val(0);
        jQuery('.ube-hidden-url').val("");
    
        //Butternut Reset
        jQuery('.flavor-hazel-butternut').attr('class', 'card-img-top flavor-hazel-butternut hazel-butternut-inactive');
        jQuery('.qty-hazel-butternut').attr('disabled', true);
        jQuery('.qty-hazel-butternut').val(0);
        jQuery('.butternut-hidden-url').val("");
    
        jQuery('.doughnut-number').html('<span class = ".doughnut-number">your</span>');
}

function findTotalQty(){
    var qtyFieldsArray = document.getElementsByName('flavor-qty');
    var tot = 0;
    var arrLen = qtyFieldsArray.length;

    for(var i = 0; i < arrLen; i++)
    {
        if(parseInt(qtyFieldsArray[i].value))
        {
            tot += parseInt(qtyFieldsArray[i].value);
        }
    }

    return tot;
}

function constructOrders(){
    var flavorInputArray = ['patis-hidden-url', 'cotabato-hidden-url', 'calamansi-hidden-url', 'meringue-hidden-url', 'ube-hidden-url', 'butternut-hidden-url'];
    var arrayLen = flavorInputArray.length;
    
    var constructedOrders = '';

    for(var i = 0; i < arrayLen; i++ ){
        if(flavorInputArray[i] != undefined || flavorInputArray[i] != '')
        {
            var currentIndexVal = flavorInputArray[i];
            constructedOrders += " " + jQuery('.'+currentIndexVal).val();
        }
    }

    return constructedOrders;
}

function calculateMaxFlavorsAllowed(boxDealID, dealQty)
{
    var maxAllowed = 0;

    if(boxDealID == 125)
    {
        maxAllowed = dealQty * 3;
        return maxAllowed;
    }
    else
    {
        maxAllowed = dealQty * 6;
        return maxAllowed;
    }
}