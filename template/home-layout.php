<?php ob_start()?>

<!--------------------------
Intro Section
---------------------------->
<a name="intro"></a>
<section id="intro" class="clearfix">
    <div class="container">
        <div class="row justify-content-center align-self-center">
            <div class="intro-top col-md-12">
                <h1>The Community Programming Language</h2>
            </div>

            <div class="intro-info col-md-6">

                <h4>Basic requirements for starting Fly project:</h4>
                <ol>
                    <li>High-level programming language making the process of developing a program simpler and more understandable.</li>
                    <li>Compiled language programming because is faster than interpreted.</li>
                    <li>A solution which implement all programming paradigms (Object Oriented, Procedural and
                        Functional) guarantees to chose better ways in different cases.
                    </li>
                    <li>A clean and easy code syntax, guessable from average programmer.</li>
                    <li>Take the best features from all know programming languages, to own all you need.</li>
                    <li>Integrate natively most know design patterns to be one step ahead.</li>
                </ol>
            </div>
            <div class="intro-img col-md-6">
                <img src="img/intro-img.png" alt="" class="img-fluid">
            </div>
        </div>
    </div>
</section>
<!-- #intro -->
<?php $intro = ob_get_clean()?>

<?php include_once "layout.php" ?>
