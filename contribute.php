<?php ob_start() ?>
<!--------------------------
Contribute Section
---------------------------->
<section>

    <div class="container">
        <h2>Contribute</h2>
        <h3>We believe in communications because an Idea borns from the right link between people, but do not forget the <a href="/code-of-conduct">Code of Conduct</a>.</h3>
        <div class="row">

            <div class="col-lg-12 col-md-12">
                <div class="content">
                    <h3><a href="https://github.com/fly-lang" class="github"><i class="fa fa-github"></i> GitHub</a></h3>
                    <p>The Fly repository where you can found project sources for giving your contribute:
                    <ul>
                        <li>All projects follow the <a href="https://opensource.guide/">Open Source Guides</a></li>
                        <li><a href="https://docs.github.com/en/github/managing-your-work-on-github/creating-an-issue" target="_blank">Create an Issue</a>
                            if you want enhancements or keep tracks of bugs.</li>
                        <li>After, if you are a member <a href="" target="_blank" href="https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/about-branches">
                            create a local branch</a> or if you not <a href="https://guides.github.com/activities/forking/"
                        target="_blank">fork the repository</a>.</li>
                        <li>Observe <a href="https://github.com/trein/dev-best-practices/wiki/Git-Commit-Best-Practices" target="_blank">Git rules and best practice</a>
                            for doing good commits.</li>
                        <li><a href="https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request" target="_blank">Create a Pull Request</a>,
                            and you will be contacted for information, code review or branch merge.</li> 
                    </ul>
                    Use <a href="https://github.com/fly-lang/fly/discussions" target="_blank">Github Discussions</a> to collect information, speak with collaborators, ask and answer,
                    or simply share your idea.
                    </p>
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3><a href="https://discord.gg/kyFHvTWUUg" class="discord" target="_blank"><i class="fa fa-discord"></i> Discord</a></h3>
                    Come on the chat channel of Fly project, here you can interact directly with members and people loving open source.
                </div>
            </div>

            <div class="col-lg-6 col-md-6">
                <div class="content">
                    <h3><a href="https://twitter.com/fly_lang" class="twitter" target="_blank"><i class="fa fa-twitter"></i> Twitter @fly_lang</a></h3>
                    This is the Fly project's official account where you can find important announcements, community updates and
                    other news.<br>
                    
                </div>
            </div>
        </div>
    </div>

</section>

<?php $main = ob_get_clean() ?>
<?php $title = "Community" ?>
<?php include_once "template/page-layout.php" ?>