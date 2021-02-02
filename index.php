<?php ob_start()?>
<!--------------------------
Project Plan Section
---------------------------->
<a name="project-plan"></a>
<section>

    <div class="container">
        <h2>Project Plan</h2>
        <h4>How the work is organized and ho to contribute in planning.</h4>
        <div class="row">

            <div class="col-lg-6 col-md-6">
                <div class="content-img">
                    <img src="img/project-plan.png" alt="">
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3>Phases</h3>
                    <p>Project is defined in 5 phase, some can run on parallel others need the completion of the
                        previous one.</p>
                    <ol>
                        <li>Define the <strong>Syntax</strong> of the language</li>
                        <li>Build the <strong>Compiler</strong></li>
                        <li>Equip with standard and auxiliary code <strong>Libraries</strong></li>
                        <li>Add <strong>Utility & Tools</strong></li>
                        <li>Write <strong>Documentation</strong></li>
                    </ol>
                    <br>
                    <!-- <h3>Management</h3>
                    <p>Every phase will be divided in tasks and in sub-tasks if necessary, each step
                        must consider advantages and disadvantages by discussing it on
                        <a href="#community">Community</a> channels.
                    </p> -->
                </div>
            </div>

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

</section><!-- #project -->

<!--------------------------
Work Progress Section
---------------------------->
<a name="work-progress"></a>
<section class="section-bg">

    <div class="container">
        <h2>Work Progress</h2>
        <h4>This contains the status of the project, in order to inform new contributors, what happening now.</p>
        <div class="row">

            <div class="col-lg-12 col-md-12">
                <div class="content">
                    <h3>We are at the start point.</h3>
                    <p>For now it's just me in the Community, I'm <a href="http://marcoromagnolo.it/en">Marco Romagnolo</a>
                        and i pushed on GitHub the lexer of the Fly compiler with a draft of indentifier keywords for the tokens.
                        In the next i will start to look for contributors to accomplish the first big task:
                        create the Syntax of the Fly programming language.</p>
                </div>
            </div>
        </div>
    </div>

</section><!-- #work-progress -->

<!--------------------------
Community Section
---------------------------->
<a name="community"></a>
<section>

    <div class="container">
        <h2>Community</h2>
        <h4>From a talk an idea borns, but do not forget the <a href="/code-of-conduct">Code of Conduct</a></p>
        <div class="row">

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3><a href="https://github.com/fly-lang" class="github"><i class="fa fa-github"></i> GitHub</a></h3>
                    Source code now contains the repository for: Fly compiler, this website and graphics.
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3><a href="https://twitter.com/fly_lang" class="twitter"><i class="fa fa-twitter"></i> Twitter</a></h3>
                    Work updates and News.
                </div>
            </div>

            <div class="col-lg-4 col-md-4">
                <div class="content">
                    <h3><a href="https://groups.google.com/g/flylang" class="google"><i class="fa fa-google"></i>oogle Group</a></h3>
                    This is the place for discussion about plans and development of Fly Language.<br>
                </div>
            </div>
        </div>
    </div>

</section><!-- #work-progress -->

<?php $page = ob_get_clean()?>
<?php include_once "template/home-layout.php" ?>