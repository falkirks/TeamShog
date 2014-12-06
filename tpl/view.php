<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <a class="navbar-brand" href="/">TOSThing</a>
        </div>
        {{#user}}
        <div id="navbar-loggedin" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>{{_id}}</li>
                <li><a href="/logout">Logout</a></li>
            </ul>
        </div>
        {{/user}}
        {{^user}}
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="/login">Login</a></li>
                <li><a href="/register">Register</a></li>
            </ul>
        </div>
        {{/user}}
    </div>
</nav>
<div class="container">
    {{#message}}
    <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        {{& message }}
    </div>
    {{/message}}
    <div class="jumbotron">
        <div class="view1">
            <h3>Original TOS</h3>
            <p>THIS IS A PLACEHOLDER</p>
        </div>
        <div class="view2">
            <h3>Translated TOS</h3>
            <p>
                <?php
                //Testing algorithm, probably won't work
                //$text = "A data center is a facility used to house computer systems and associated components, such as telecommunications and storage systems. It generally includes redundant or backup power supplies, redundant data communications connections, environmental controls (e.g., air conditioning, fire suppression) and various security devices. Large data centers are industrial scale operations using as much electricity as a small town[1] and sometimes are a significant source of air pollution in the form of diesel exhaust.[2] Capabilities exist to install modern retrofit devices on older diesel generators, including those found in data centers, to reduce emissions.[3] Additionally, engines manufactured in the U.S. beginning in 2014 must meet strict emissions reduction requirements according to the U.S. Environmental Protection Agency's 'Tier 4' regulations for off-road uses including those found in diesel generators. These regulations require near zero levels of emissions.";
                //echo water\api\algorithm::Summarize($text,0.25,1,0);
                //Testing API 1
                $params = array('text' => 'John is a very good football player!');
                $sentiment = water\api\aylien::call_api('sentiment', $params);
                $language = water\api\aylien::call_api('language', $params);

                echo sprintf("Sentiment: %s (%F)", $sentiment->polarity, $sentiment->polarity_confidence),
                PHP_EOL;
                echo sprintf("Language: %s (%F)", $language->lang, $language->confidence),
                PHP_EOL;
                ?>
            </p>
        </div>
    </div>
</div>