<link rel="stylesheet" href="/public/css/account.css">
<div class="section manage">
    <div class="header"><?=$subtitle?></div>
    <div class="content">
        <div class="subheader">Your account information:</div>
        <div class="media">
            <div onclick="location = '/account/avatar'" data-tooltip="Change Avatar" class="avatar<?=$information['gmlevel'] > 0 ? ' admin' : ''?>" style="--image: url('/public/img/avatar/<?=$information['avatar']?>.webp')"></div>
            <div onclick="location = '/account/signature'" data-tooltip="Change Signature" class="signature" style="--image: url('/public/img/signature/<?=$information['signature']?>.webp')"></div>
        </div>
        <div class="account">
            <div class="tags">
                <div>Nickname</div>
                <div>Username</div>
                <div>Email</div>
                <div>IP</div>
                <div>Platform</div>
            </div>
            <div class="data">
                <div><?=$information['nickname']?></div>
                <div><?=$information['username']?></div>
                <div><?=$information['email']?></div>
                <div><?=$information['ip']?></div>
                <div><?=$information['platform']?></div>
            </div>
            <div class="actions">
                <a href="/account/nickname">Change Nickname</a>
                <a href="/account/password">Change Password</a>
                <a href="/account/email">Change Email</a>
                <a href="/account/mfa"><?=$information['mfa'] ? 'Disable' : 'Enable'?> MFA</a>
                <a href="/account/logs">Access Logs</a>
            </div>
        </div>
<?
    if (count($realms) > 0) {
        foreach ($realms as $realm) {
?>
        <div class="subheader">Your characters on our '<?=$realm['name']?>' realm:</div>
        <div class="table characters">
            <div class="header">
                <div class="id">#</div>
                <div class="name">Name</div>
                <div class="level">Level</div>
                <div class="faction">Faction</div>
                <div class="race">Race</div>
                <div class="class">Class</div>
                <div class="money">Money</div>
                <div class="playtime">Playtime</div>
            </div>
<?
            if (count($realm['characters']) > 0) {
                foreach ($realm['characters'] as $index => $character) {
?>
            <div class="row<?=' ' . $realm['version']?>">
                <div class="id"><?=$index+1?></div>
                <div class="name"><?=$character['name']?></div>
                <div class="level"><?=$character['level']?></div>
                <div class="faction"><img data-tooltip="<?=$character['faction']?>" alt="<?=$character['faction']?>" src="/public/img/icons/faction/<?=lcfirst($character['faction'])?>.webp"/></div>
                <div class="race"><img data-tooltip="<?=$character['race_name'] . ' • ' . ($character['gender'] ? 'Female' : 'Male')?>" src="/public/img/icons/race/<?=$character['race'] . '-' . $character['gender']?>.webp"/></div>
                <div class="class"><img data-tooltip="<?=$character['class_name']?>" src="/public/img/icons/class/<?=$character['class']?>.webp"/></div>
                <div class="money">
                    <img data-tooltip="Gold" src="/public/img/icons/currency/gold.webp"/><?=$character['money']['gold']?>
                    <img data-tooltip="Silver" src="/public/img/icons/currency/silver.webp"/><?=$character['money']['silver']?>
                    <img data-tooltip="Copper" src="/public/img/icons/currency/copper.webp"/><?=$character['money']['copper']?>
                </div>
                <div class="playtime"><?=$character['playtime']?></div>
            </div>
<?
                }
            } else {
?>
            <div class="row error">
                <div>You currently have no characters on this realm.</div>
            </div>
<?
            }
?>
        </div>
<?
        }
    }
?>
    </div>
</div>