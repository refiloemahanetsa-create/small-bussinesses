<?php

// Load the language system
require_once "language.php";

?>

<!DOCTYPE html>
<html lang="<?php echo $_SESSION["language"] === "st" ? "st" : "en"; ?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo t("site_name"); ?>
    </title>


    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {
            background-color: #f5f7f6;
            color: #333;
            
        }


        /* Navigation */

        nav {
            background-color: #00ad2e;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }


        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }


        .nav-links {
            display: flex;
            gap: 15px;
            align-items: center;
        }


        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 16px;
        }


        .nav-links a:hover {
            text-decoration: underline;
        }


        .register-btn {
            color: white !important;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }


        .login-btn {
            color: white !important;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }


        /* Language Selector */

        .language-selector {
            padding: 9px 10px;
            border: none;
            border-radius: 5px;
            background-color: white;
            color: #333;
            font-size: 14px;
            cursor: pointer;
        }


        /* Hero Section */

        .hero {
            min-height: 500px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 70px 7%;
            background-color: white;
        }


        .hero-text {
            width: 50%;
        }


        .hero-text h1 {
            font-size: 48px;
            line-height: 1.2;
            margin-bottom: 20px;
            color: #222;
        }


        .hero-text h1 span {
            color: #00ad2e;
        }


        .hero-text p {
            font-size: 18px;
            line-height: 1.7;
            margin-bottom: 30px;
            color: #555;
        }


        .hero-box {
            width: 40%;
            min-height: 280px;
            background-color: #e9f8ed;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }


        .hero-box h2 {
            color: #00ad2e;
            font-size: 30px;
        }


        /* Features */

        .features {
            padding: 60px 7%;
            text-align: center;
        }


        .features h2 {
            font-size: 32px;
            margin-bottom: 40px;
        }


        .feature-container {
            display: flex;
            gap: 25px;
            justify-content: center;
        }


        .feature {
            background-color: white;
            padding: 30px;
            width: 30%;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }


        .feature h3 {
            color: #00ad2e;
            margin-bottom: 15px;
        }


        .feature p {
            line-height: 1.6;
            color: #666;
        }


        /* Call to Action */

        .cta {
            background-color: #00ad2e;
            color: white;
            text-align: center;
            padding: 60px 20px;
        }


        .cta h2 {
            font-size: 32px;
            margin-bottom: 15px;
        }


        .cta p {
            margin-bottom: 25px;
            font-size: 17px;
        }


        .cta a {
            display: inline-block;
            background-color: white;
            color: #00ad2e;
            padding: 13px 28px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }


        .cta a:hover {
            background-color: #f0f0f0;
        }


        /* Footer */

        footer {
            background-color: #222;
            color: white;
            text-align: center;
            padding: 20px;
        }


        /* Mobile */

        @media (max-width: 768px) {

            nav {
                flex-direction: column;
                gap: 15px;
            }


            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }


            .hero {
                flex-direction: column;
                text-align: center;
            }


            .hero-text {
                width: 100%;
            }


            .hero-text h1 {
                font-size: 36px;
            }


            .hero-box {
                width: 100%;
                margin-top: 40px;
            }


            .feature-container {
                flex-direction: column;
            }


            .feature {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- Navigation -->

<nav>


    <div class="logo">

        <?php echo t("site_name"); ?>

    </div>


    <div class="nav-links">


        <a href="index.php">

            <?php echo t("home"); ?>

        </a>


        <!-- Language Selector -->

       
<?php require_once __DIR__ . '/language.php'; ?>

<form action="index.php" method="GET">

    <select name="lang" id="lang"
            onchange="this.form.submit()">

        <option value="en"
            <?= $_SESSION['language'] === 'en'
                ? 'selected' : '' ?>>
            English
        </option>

        <option value="st"
            <?= $_SESSION['language'] === 'st'
                ? 'selected' : '' ?>>
            Sesotho
        </option>

    </select>
</form>

        <a
            href="register.php"
            class="register-btn"
        >

            <?php echo t("register"); ?>

        </a>


        <a
            href="login.php"
            class="login-btn"
        >

            <?php echo t("login"); ?>

        </a>


    </div>

</nav>



<!-- Hero Section -->

<section class="hero">


    <div class="hero-text">


        <h1>

            <?php if ($_SESSION["language"] === "st"): ?>

                Tšehetsa
                <span>Likhoebo tsa Lehae</span>
                'me U Reke Inthaneteng

            <?php else: ?>

                Support
                <span>Local Businesses</span>
                and Shop Online

            <?php endif; ?>

        </h1>


        <p>

            <?php if ($_SESSION["language"] === "st"): ?>

                Fumana lihlahisoa tse tsoang likhoebong tse nyane
                tsa lehae, tšehetsa bo-rakhoebo sechabeng sa heno,
                'me u natefeloe ke mokhoa o bonolo le o bonolo
                oa ho reka inthaneteng.

            <?php else: ?>

                Discover products from local small businesses,
                support entrepreneurs in our community, and enjoy
                a simple and convenient online shopping experience.

            <?php endif; ?>

        </p>


    </div>



    <div class="hero-box">


        <div>

            <h2>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Reka Haholo.<br>
                    Tšehetsa Haholo.

                <?php else: ?>

                    Shop Local.<br>
                    Support Local.

                <?php endif; ?>

            </h2>

        </div>


    </div>

</section>



<!-- Features -->

<section class="features">


    <h2>

        <?php if ($_SESSION["language"] === "st"): ?>

            Seo Re Fanang ka Sona

        <?php else: ?>

            What We Offer

        <?php endif; ?>

    </h2>



    <div class="feature-container">


        <!-- Feature 1 -->

        <div class="feature">


            <h3>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Lihlahisoa tsa Lehae

                <?php else: ?>

                    Local Products

                <?php endif; ?>

            </h3>


            <p>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Fumana lihlahisoa le litšebeletso tse fanoang
                    ke likhoebo tse nyane sechabeng sa heno.

                <?php else: ?>

                    Find products and services offered by
                    small businesses in your community.

                <?php endif; ?>

            </p>


        </div>



        <!-- Feature 2 -->

        <div class="feature">


            <h3>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Ho Reka ha Bonolo

                <?php else: ?>

                    Easy Shopping

                <?php endif; ?>

            </h3>


            <p>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Sheba lihlahisoa 'me u behe litaelo
                    ka mokhoa o bonolo oa ho reka inthaneteng.

                <?php else: ?>

                    Browse products and place orders through
                    a simple online shopping platform.

                <?php endif; ?>

            </p>


        </div>



        <!-- Feature 3 -->

        <div class="feature">


            <h3>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Tšehetsa Likhoebo

                <?php else: ?>

                    Support Businesses

                <?php endif; ?>

            </h3>


            <p>

                <?php if ($_SESSION["language"] === "st"): ?>

                    Thusa likhoebo tse nyane ho fihlela bareki
                    ba bangata le ho holisa likhoebo tsa tsona
                    ka theknoloji.

                <?php else: ?>

                    Help small businesses reach more customers
                    and grow their businesses through technology.

                <?php endif; ?>

            </p>


        </div>


    </div>

</section>



<!-- Call to Action -->

<section class="cta">


    <h2>

        <?php if ($_SESSION["language"] === "st"): ?>

            U Loketse ho Qala ho Reka?

        <?php else: ?>

            Ready to Start Shopping?

        <?php endif; ?>

    </h2>


    <p>

        <?php if ($_SESSION["language"] === "st"): ?>

            Theha ak'haonte ea hau 'me u fumane seo
            likhoebo tsa lehae li fanang ka sona.

        <?php else: ?>

            Create your account and discover what local
            businesses have to offer.

        <?php endif; ?>

    </p>


    <a href="register.php">

        <?php if ($_SESSION["language"] === "st"): ?>

            Theha Ak'haonte

        <?php else: ?>

            Create an Account

        <?php endif; ?>

    </a>


</section>



<!-- Footer -->

<footer>


    <p>

        &copy;
        <?php echo date("Y"); ?>

        <?php echo t("site_name"); ?>.

        <?php if ($_SESSION["language"] === "st"): ?>

            Litokelo Tsohle Li Sirelelitsoe.

        <?php else: ?>

            All Rights Reserved.

        <?php endif; ?>

    </p>


</footer>



<!-- Language JavaScript -->

<script>

function changeLanguage(language) {

    window.location.href = "index.php?lang=" + language;

}

</script>


</body>

</html>