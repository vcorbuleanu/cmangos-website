<!doctype html>
<html lang="en">
    <head>
        <title><?=WEBSITE_TITLE?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <link rel="icon" type="image/x-icon" href="/public/img/favicon.ico">
        <link rel="stylesheet" href="/public/css/style.css">
        <script type="text/javascript" src="/public/js/theme.js"></script>
        <script type="text/javascript" src="/public/js/tooltip.js"></script>
    </head>
    <body>
        <div class="header"></div>
        <div class="body">
            <div class="menu">
                <div class="button" id="m1">
                    <div class="text">NEWS</div>
                    <div class="icon news"></div>
                    <div class="status"></div>
                    <div class="sub-menu">
                        <a href="/news/latest" class="item">Latest</a>
                        <a href="/news/archived" class="item">Archived</a>
                    </div>
                </div>
                <div class="button" id="m2">
                    <div class="text">ACCOUNT</div>
                    <div class="icon account"></div>
                    <div class="status"></div>
                    <div class="sub-menu">
                        <? if ($authenticated) { ?>
                        <a href="/account/manage" class="item">Manage</a>
                        <a id="logout" href="/account/logout" class="item">Logout</a>
                        <? } else { ?>
                        <a href="/account/create" class="item">Create</a>
                        <a href="/account/login" class="item">Login</a>
                        <a href="/account/recover" class="item">Recover</a>
                        <? } ?>
                    </div>
                </div>
                <div class="button" id="m3">
                    <div class="text">GUIDE</div>
                    <div class="icon guide"></div>
                    <div class="status"></div>
                    <div class="sub-menu">
                        <a href="/guide/races" class="item">Races</a>
                        <a href="/guide/classes" class="item">Classes</a>
                        <a href="/guide/professions" class="item">Professions</a>
                        <a href="/guide/factions" class="item">Factions</a>
                        <a href="/guide/locations" class="item">Locations</a>
                        <a href="/guide/dungeons" class="item">Dungeons</a>
                        <a href="/guide/instances" class="item">Instances</a>
                    </div>
                </div>
                <div class="button" id="m4">
                    <div class="text">STORY</div>
                    <div class="icon story"></div>
                    <div class="status"></div>
                    <div class="sub-menu">
                        <a href="/story/history" class="item">History</a>
                        <a href="/story/lore" class="item">Lore</a>
                    </div>
                </div>
                <div class="button" id="m5">
                    <div class="text">REALMS</div>
                    <div class="icon realms"></div>
                    <div class="status"></div>
                    <div class="sub-menu">
                        <a href="/realms/status" class="item">Status</a>
                        <a href="/realms/players" class="item">Players</a>
                        <a href="/realms/statistics" class="item">Statistics</a>
                        <a href="/realms/guilds" class="item">Guilds</a>
                        <a href="/realms/honor" class="item">Honor</a>
                        <a href="/realms/arena" class="item">Arena</a>
                        <a href="/realms/battlegrounds" class="item">Battlegrounds</a>
                    </div>
                </div>
            </div>
            <div class="page">
                <div onclick="change_theme()" class="title"><?=WEBSITE_TITLE . ' • ' . $title?></div>
                <div class="banner" style="--banner: url(<?=$banner?>);"></div>