<?php ob_start() ?>

<!--------------------------
Intro Section
---------------------------->

<section id="intro" class="clearfix">
    <div class="container">
        <div class="row justify-content-center align-self-center">
            <div class="intro-top col-md-12">

            </div>

            <div class="intro-info col-md-8">
                <h1>Fly Programming Language</h1>
                <ul>
                    <li><h3>This is a <strong>New Project</strong> for <strong>Old Programmers</strong></h3></li>
                    <li><h3>Focused on the <strong>Best Features</strong> from other languages</h3></li>
                    <li><h3>Growing, Easy, Fast, Versatile</h3></li>
                </ul>
            </div>
            <div class="intro-img col-md-4">
                <img src="img/intro-img.png" alt="" class="img-fluid">
            </div>
            <div class="margin-top-100 col-md-12">
                <button type="button" onclick="download('0.0.4-alpha')" class="btn btn-primary btn-lg">Download latest (0.0.4-alpha)</button>
                <a type="button" href="#get-started" class="btn btn-light btn-outline-info btn-lg">Get Started</a>
            </div>
        </div>
    </div>
</section>
<!-- #intro -->

<!--------------------------
Home Section
---------------------------->
<section>

    <div class="container">
        <h2>Project Plan</h2>
        <h4>What are the projects goals? What kind of feautures does the language needs to be better than others?</h4>
        <div class="row">

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3>Goals</h3>
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
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3>Phases</h3>
                    <ol>
                        <li>Define the <strong>Syntax</strong> of the language</li>
                        <li>Build the <strong>Compiler</strong></li>
                        <li>Equip with standard and auxiliary code <strong>Libraries</strong></li>
                        <li>Add <strong>Utility & Tools</strong></li>
                        <li>Write <strong>Documentation</strong></li>
                    </ol>
                    <br>

                </div>
            </div>
        </div>

        <div class="col-lg-12 col-md-12">
            <div class="content">
                <h2>Project Status</h2>
                <h3>We are at the start point</h3>
                <p>The community is just born, we are a few, we need to grow, please share this initiative, <a href="/contribute.php">contribute</a>.</p>
                <h4>The first step is Plan for creating a new Syntax Language:</h4>
                <ul>
                    <li>
                        <h4>Follow more details in <a href="/project.php">The Project</a></h4>
                    </li>
                    <li>
                        <h4>Start a discussion on <a href="https://github.com/fly-lang/fly/discussions" target="_blank">Github</a></h4>
                    </li>
                    <li>
                        <h4>Or enter in chat on <a href="https://discord.gg/kyFHvTWUUg" target="_blank">Discord</a></h4>
                    </li>
                    <li>
                        <h4>Propose your best features for Fly programming language!</h4>
                    </li>
                </ul>
            </div>
        </div>

    </div>

</section><!-- #project -->

<?php $main = ob_get_clean() ?>
<?php $title = "Home" ?>
<?php include_once "template/layout.php" ?>