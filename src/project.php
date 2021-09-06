<?php ob_start() ?>
<!--------------------------
Community Section
---------------------------->
<section id="intro">

    <div class="container">
        <h2>The Project</h2>
        <h3>Requirements and Guidelines</h3>

        <div class="row justify-content-left align-self-center">
            <div class="col-lg-12 col-md-12">
                <div class="content">
                    <h3>The Syntax</h3>
                    <p>This is the first phase and the most important because will determine the programming
                        language usability. It's preferable not to upset and not to invent strange things, but
                        use what a programmer already know. New features will be added in the next releases.<br>
                        Consider the following features taken from known programming language:<br>
                    <ul>
                        <li>Duck Typing from Python</li>
                        <li>Overloading Operators from Python and C++</li>
                        <li>Package import management like Python or Go</li>
                        <li>Aggregator Type already integrated like Python and Rust</li>
                        <li>Template like C++, or Generic Type like Java</li>
                        <li>Visibility scope and Functional Programming like Java</li>
                        <li>Object Oriented and Procedural like Python and C++</li>
                        <li>GC management like Go</li>
                    </ul>
                    </p>
                </div>
            </div>

            <div class="col-lg-12 col-md-12">
                <div class="content">
                    <h3>The Compiler</h3>
                    <p> The core of the language is based on <a href="https://llvm.org/">LLVM compiler</a>, because is an incredible project wich already
                        collects a large number of trophies in various aspects, first of all performance.
                        It already owns all that a compiler needs, is well documented and permit to convert sources in IR for different kind of
                        hardware architectures.
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3>Libraries</h3>
                    <p>Any programming language have a base library to do common operations, interact with OS and
                        external resources like:
                    <ul>
                        <li>Standard library</li>
                        <li>Math library</li>
                        <li>File input/output library</li>
                        <li>Networking library</li>
                        <li>Security library</li>
                    </ul>
                    </p>
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3>Utility & Tools</h3>
                    <p><strong>Compiler Frontend</strong> to launch compilation from command line interface and
                        specify options.</p>
                    <p><strong>Package Manager</strong> for easy library installation with a common repository (like
                        Pip for Python).</p>
                    <p><strong>Graphics</strong> includes logo, banners, fonts, pictures and all media related to
                        the project ready to use use in web sites, socials and prints.</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3>Documentation</h3>
                    <p><strong>GitHub Wiki</strong> with main docs for developers.</p>
                    <p><strong>Fly Lang Wiki (todo)</strong> contains detailed information about Fly Project</p>
                    <p><strong>Google Group Mailing List</strong>To share planning, working problem, issue, new features</p>
                </div>
            </div>

        </div>
    </div>

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

</section>

<?php $main = ob_get_clean() ?>
<?php $title = "The Project" ?>
<?php include_once "template/layout.php" ?>