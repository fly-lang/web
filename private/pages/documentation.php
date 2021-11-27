<?php
$title = 'Documentation';
?>

<!--------------------------
Documentation Section
---------------------------->
<section id="intro">

    <div class="container">
        <h2>Documentation</h2>

        <div class="content">
            <h3>Wiki</h3>
            <p>The official documentation is directly integrated into
                <a target="_blank" href="<? echo $WIKI_URL?>" target="_blank">Github Wiki</a> pages:</p>
            <ul>
                <li><a href="<? echo $WIKI_LANGUAGE_REFERENCE_URL?>" target="_blank">Language Reference</a></li>
                <li><a href="<? echo $WIKI_COMMAND_GUIDE_URL?>" target="_blank">Command Guide</a></li>
                <li><a href="<? echo $WIKI_PROGRAMMERS_MANUAL_URL?>" target="_blank">Programmer's Manual</a></li>
            </ul>
        </div>

        <div class="content">
            <h3>Overview</h3>
            <p>Fly was born in 2020 as an alternative to some of the most popular programming languages, with these main
                requirements:</p>
            <ul>
                <li>To be fast</li>
                <li>Easy to write and understand</li>
                <li>Good, memorable syntax that was quick to write</li>
                <li>To be powerful for all application</li>
            </ul>
            <p><i>Our guideline is that: "This programming language doesn't have to do anything special, but to just
                    make things simpler than others."</i>
            </p>
        </div>

        <div class="content">
            <h3>Top Features</h3>
            <ul>
                <li>Fly is based on <a target="_blank" href="https://llvm.org">LLVM</a>: backend optimized and exportable for multiple platforms.</li>
                <li>Multi-paradigm: Procedural, Object oriented, functional programming.</li>
                <li>Duck Typing Inheritance</li>
                <li>Exception handling without try-catch statement</li>
                <li>Native aggregator types.</li>
                <li>Package import management based on namespace.</li>
                <li>Open source</li>
            </ul>
        </div>

        <div class="content">
            <h3>Project Status</h3>
            <p>The project is at an <strong>embryonic state</strong> with Prerelease version.<br>
                You will be able to find more details about all of the working features and the state of the project in
                the <a target="_blank" href="<?php echo $WIKI_URL?>>">Official Wiki</a>.
            </p>
        </div>
    </div>
</section>
