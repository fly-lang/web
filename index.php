<?php ob_start() ?>
<!--------------------------
Project Plan Section
---------------------------->
<section>
    <div class="container">
        <h4>If you are thinking: "But I already use a fantastic programming language, it is..."</h4>
        <div class="row">
            <div class="col-md-4">
                <img src="img/languages_1.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-4">
                <img src="img/languages_2.png" alt="" class="img-fluid">
            </div>
            <div class="col-md-4">
                <img src="img/languages_3.png" alt="" class="img-fluid">
            </div>
        </div>
    </div>

    <div class="container">
        <h2>Project Plan</h2>
        <h4>What are the projects goals? What kind of feautures does the language needs to be better than others?</h4>
        <div class="row">

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3>Goals</h4>
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
<?php include_once "template/home-layout.php" ?>