<link rel="stylesheet" href="/public/css/avatar.css">
<div class="section avatar">
    <div class="header"><?=$subtitle?></div>
    <div class="content">
        <div class="subheader">List of available avatars:</div>
        <form method="post">
            <div class="avatar-list">
<?
    if (count(AVATAR_LIST) > 0)
        foreach (AVATAR_LIST as $image) {
?>
            <label class="option" data-tooltip="<?=ucwords(str_replace('_', ' ', $image))?>">
                <input type="radio" name="avatar" value="<?=$image?>"<?=$information['avatar'] === $image ? ' checked="checked"' : ''?>>
                <div class="avatar<?=$information['gmlevel'] > 0 ? ' admin' : ''?>" style="--image: url('/public/img/avatar/<?=$image?>.webp')"></div>
            </label>
<?
        }
?>
            </div>
            <input type='submit' value='Change'/>
        </form>
    </div>
</div>