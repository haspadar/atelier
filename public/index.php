<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.1.1/flatly/bootstrap.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css"/>
    <link rel="stylesheet" href="/css/menu.css"/>
    <link rel="stylesheet" href="/css/atelier.css"/>
</head>
<body class="d-flex flex-column min-vh-100">
<nav class="offcanvas offcanvas-start show" tabindex="-1" id="offcanvas" data-bs-keyboard="false"
     data-bs-backdrop="true" data-bs-scroll="true">
    <div class="offcanvas-header border-bottom">
        <h4>
            <a class="navbar-brand" href="#">
                <img src="/img/atelier4.svg" height="30" class="d-inline-block align-top" alt="logo">
                Atelier
            </a>
        </h4>
    </div>
    <div class="offcanvas-body px-0">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Гардероб</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Гараж</a>
            </li>
        </ul>
<!--        <ul class="list-unstyled ps-0">-->
<!--            <li class="mb-1">-->
<!--                <button class="btn btn-toggle align-items-center rounded" data-bs-toggle="collapse"-->
<!--                        data-bs-target="#wardrobe-collapse" aria-expanded="true">-->
<!--                    Гардероб-->
<!--                </button>-->
<!--                <div class="collapse show" id="wardrobe-collapse" style="">-->
<!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
<!--                        <li><a href="/" class="rounded">Overview</a></li>-->
<!--                        <li><a href="#" class="rounded">Updates</a></li>-->
<!--                        <li><a href="#" class="rounded">Reports</a></li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li class="mb-1">-->
<!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
<!--                        data-bs-target="#dashboard-collapse" aria-expanded="false">-->
<!--                    Dashboard-->
<!--                </button>-->
<!--                <div class="collapse" id="dashboard-collapse">-->
<!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
<!--                        <li><a href="#" class="rounded">Overview</a></li>-->
<!--                        <li><a href="#" class="rounded">Weekly</a></li>-->
<!--                        <li><a href="#" class="rounded">Monthly</a></li>-->
<!--                        <li><a href="#" class="rounded">Annually</a></li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li class="mb-1">-->
<!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
<!--                        data-bs-target="#orders-collapse" aria-expanded="false">-->
<!--                    Orders-->
<!--                </button>-->
<!--                <div class="collapse" id="orders-collapse">-->
<!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
<!--                        <li><a href="#" class="rounded">New</a></li>-->
<!--                        <li><a href="#" class="rounded">Processed</a></li>-->
<!--                        <li><a href="#" class="rounded">Shipped</a></li>-->
<!--                        <li><a href="#" class="rounded">Returned</a></li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </li>-->
<!--            <li class="border-top my-3"></li>-->
<!--            <li class="mb-1">-->
<!--                <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse"-->
<!--                        data-bs-target="#account-collapse" aria-expanded="false">-->
<!--                    Account-->
<!--                </button>-->
<!--                <div class="collapse" id="account-collapse">-->
<!--                    <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">-->
<!--                        <li><a href="#" class="rounded">New...</a></li>-->
<!--                        <li><a href="#" class="rounded">Profile</a></li>-->
<!--                        <li><a href="#" class="rounded">Settings</a></li>-->
<!--                        <li><a href="#" class="rounded">Sign out</a></li>-->
<!--                    </ul>-->
<!--                </div>-->
<!--            </li>-->
<!--        </ul>-->
    </div>
</nav>
<main class="container">
    <div class="row">
        <div class="col p-4">
            <!-- toggler -->
            <button id="sidebarCollapse" class="float-end" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                    role="button" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </button>


            <h1>Bootstrap 5 Offcanvas Sidebar Skeleton Example</h1><span
                    class="ezoic-autoinsert-ad ezoic-top_of_page"></span><span style="clear:both; display:block"
                                                                               id="ez-clearholder-box-2"></span><span
                    class="ezoic-ad box-2 box-2220 adtester-container adtester-container-220"
                    data-ez-name="cssscript_com-box-2"><span id="div-gpt-ad-cssscript_com-box-2-0" ezaw="250" ezah="250"
                                                             style="position:relative;z-index:0;display:inline-block;padding:0;min-height:250px;min-width:250px;"
                                                             class="ezoic-ad"><script data-ezscrex="false"
                                                                                      data-cfasync="false"
                                                                                      type="text/javascript"
                                                                                      style="display:none;">if (typeof ez_ad_units != 'undefined') {
                            ez_ad_units.push([[250, 250], 'cssscript_com-box-2', 'ezslot_0', 220, '0', '0'])
                        }
                        ;
                        if (typeof __ez_fad_position != 'undefined') {
                            __ez_fad_position('div-gpt-ad-cssscript_com-box-2-0')
                        }
                        ;</script></span></span>
            <div style="margin:30px auto">
                <div id="carbon-block"></div>
            </div>
            <p class="lead">This is a responsive off-canvas sidebar navigation built using Bootstrap 5&#39;s offcanvas
                component.</p>
            <p>Sriracha biodiesel taxidermy organic post-ironic, Intelligentsia salvia mustache 90&#39;s code editing
                brunch. Butcher polaroid VHS art party, hashtag Brooklyn deep v PBR narwhal sustainable mixtape swag
                wolf squid tote bag. Tote bag cronut semiotics, raw denim deep v taxidermy messenger bag. Tofu YOLO
                Etsy, direct trade ethical Odd Future jean shorts paleo. Forage Shoreditch tousled aesthetic irony,
                street art organic Bushwick artisan cliche semiotics ugh synth chillwave meditation. Shabby chic lomo
                plaid vinyl chambray Vice. Vice sustainable cardigan, Williamsburg master cleanse hella DIY 90&#39;s
                blog.</p>
            <p>Ethical Kickstarter PBR asymmetrical lo-fi. Dreamcatcher street art Carles, stumptown gluten-free
                Kickstarter artisan Wes Anderson wolf pug. Godard sustainable you probably haven&#39;t heard of them,
                vegan farm-to-table Williamsburg slow-carb readymade disrupt deep v. Meggings seitan Wes Anderson
                semiotics, cliche American Apparel whatever. Helvetica cray plaid, vegan brunch Banksy leggings +1
                direct trade. Wayfarers codeply PBR selfies. Banh mi McSweeney&#39;s Shoreditch selfies, forage
                fingerstache food truck occupy YOLO Pitchfork fixie iPhone fanny pack art party Portland.</p><span
                    class="ezoic-autoinsert-ad ezoic-under_first_paragraph"></span><span
                    style="clear:both; display:block" id="ez-clearholder-medrectangle-3"></span><span
                    class="ezoic-ad medrectangle-3 medrectangle-3320 adtester-container adtester-container-320"
                    data-ez-name="cssscript_com-medrectangle-3"><span id="div-gpt-ad-cssscript_com-medrectangle-3-0"
                                                                      ezaw="580" ezah="400"
                                                                      style="position:relative;z-index:0;display:inline-block;padding:0;min-height:400px;min-width:580px;"
                                                                      class="ezoic-ad"><script data-ezscrex="false"
                                                                                               data-cfasync="false"
                                                                                               type="text/javascript"
                                                                                               style="display:none;">if (typeof ez_ad_units != 'undefined') {
                            ez_ad_units.push([[580, 400], 'cssscript_com-medrectangle-3', 'ezslot_1', 320, '0', '0'])
                        }
                        ;
                        if (typeof __ez_fad_position != 'undefined') {
                            __ez_fad_position('div-gpt-ad-cssscript_com-medrectangle-3-0')
                        }
                        ;</script></span></span> Sed ut perspiciatis unde omnis iste natus error sit voluptatem
            accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi
            architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut
            odit aut fugit, sed quia cor magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam
            est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi
            tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis
            nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?
        </div>
    </div>
</main>
<footer class="container text-muted border-top mt-auto">
    <div class="row">
        <span class="ezoic-autoinsert-ad ezoic-bottom_of_page"></span><span style="clear:both; display:block"
                                                                            id="ez-clearholder-medrectangle-1"></span><span
                class="ezoic-ad medrectangle-1 medrectangle-1280 adtester-container adtester-container-280"
                data-ez-name="cssscript_com-medrectangle-1"><span id="div-gpt-ad-cssscript_com-medrectangle-1-0"
                                                                  ezaw="300" ezah="250"
                                                                  style="position:relative;z-index:0;display:inline-block;padding:0;min-height:250px;min-width:300px;"
                                                                  class="ezoic-ad"><script data-ezscrex="false"
                                                                                           data-cfasync="false"
                                                                                           type="text/javascript"
                                                                                           style="display:none;">if (typeof ez_ad_units != 'undefined') {
                        ez_ad_units.push([[300, 250], 'cssscript_com-medrectangle-1', 'ezslot_2', 280, '0', '0'])
                    }
                    ;
                    if (typeof __ez_fad_position != 'undefined') {
                        __ez_fad_position('div-gpt-ad-cssscript_com-medrectangle-1-0')
                    }
                    ;</script></span><span style="width:300px;display:block;height:14px;margin:auto" class="reportline"><span
                        style="text-align:center;font-size: smaller;float:left;line-height:normal;"><a
                            href="https://www.ezoic.com/what-is-ezoic/" target="_blank"
                            rel="noopener noreferrer nofollow" style="cursor:pointer"><img
                                src="https://go.ezoic.net/utilcave_com/img/ezoic.png" alt="Ezoic" loading="lazy"
                                style="height:12px !important; padding:2px !important; border:0px !important; cursor:pointer !important; width: 58px !important; margin:0 !important; box-sizing: content-box !important;"/></a></span><span
                        class="ez-report-ad-button"
                        name="?pageview_id=7a161ed8-ff68-4f05-6489-39f48cd30008&amp;ad_position_id=4&amp;impression_group_id=cssscript_com-medrectangle-1/2022-07-08/1053891733049655&amp;ad_size=300x250&amp;domain_id=14476&amp;url=https://www.cssscript.com/demo/responsive-sidebar-bootstrap-offcanvas/"
                        style="cursor: pointer!important; font-size:12px !important;color: #a5a5a5 ;float:right;text-decoration:none !important;font-family:arial !important;line-height:normal;">report this ad</span></span></span>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ"
        crossorigin="anonymous"></script>
<script src="/js/menu.js"></script>
<!--https://www.cssscript.com/responsive-sidebar-bootstrap-offcanvas/-->
</body>
</html>