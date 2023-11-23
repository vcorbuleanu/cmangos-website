<link rel="stylesheet" href="/public/css/realms.css">
<link rel="stylesheet" href="/public/css/_404.css">
<?
        if (count($realms) > 0) {
?>
<div class="section">
    <div class="header">Players</div>
    <div class="content">
        <?
            foreach ($realms as $realm) {
        ?>
        <div class="subheader">Online players for our '<?=$realm['name']?>' realm:</div>
        <div class="table players">
            <div class="header">
                <div class="id">#</div>
                <div class="name">Name</div>
                <div class="level">Level</div>
                <div class="faction">Faction</div>
                <div class="race">Race</div>
                <div class="class">Class</div>
                <div class="playtime">Playtime</div>
            </div>
            <?
                if (count($realm['players']) > 0) {
                    foreach ($realm['players'] as $index => $player) {
            ?>
            <div class="row<?=' ' . $realm['version']?>">
                <div class="id"><?=$index+1?></div>
                <div class="name"><?=$player['name']?></div>
                <div class="level"><?=$player['level']?></div>
                <div class="faction">
                    <img
                        data-tooltip="<?=$player['faction']?>"
                        alt="<?=$player['faction']?>"
                        src="/public/img/icons/faction/<?=lcfirst($player['faction'])?>.webp"/>
                </div>
                <div class="race">
                    <img data-tooltip="<?=$player['race_name'] . ' • ' . ($player['gender'] ? 'Female' : 'Male')?>" src="/public/img/icons/race/<?=$player['race'] . '-' . $player['gender']?>.webp"/>
                </div>
                <div class="class">
                    <img data-tooltip="<?=$player['class_name']?>" src="/public/img/icons/class/<?=$player['class']?>.webp"/>
                </div>
                <div class="playtime"><?=$player['playtime']?></div>
            </div>
            <?
                    }
                } else {
            ?>
            <div class="row error">
                <div>There are currently no online players for this realm.</div>
            </div>
            <?
                }
            ?>
        </div>
        <?
            }
        ?>
    </div>
</div>
<?
        } else {
?>
<div class="section _404">
    <div class="header">Statistics</div>
    <div class="content">
        <div class="warning">
            <p>“I couldn't find any realms in the database.”</p>
        </div>
        <div class="orc"></div>
    </div>
</div>
<?
        }
?>