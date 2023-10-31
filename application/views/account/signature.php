<link rel="stylesheet" href="/public/css/signature.css">
<div class="section signature">
    <div class="header"><?=$subtitle?></div>
    <div class="content">
        <div class="subheader">List of available signatures:</div>
        <form method="post">
            <div class="signature-list">
<?
    if (count(SIGNATURE_LIST) > 0)
        foreach (SIGNATURE_LIST as $image) {
?>
            <label class="option" data-tooltip="<?=ucwords(str_replace('_', ' ', $image))?>">
                <input type="radio" name="signature" value="<?=$image?>"<?=$information['signature'] === $image ? ' checked="checked"' : ''?>>
                <div class="signature" style="--image: url('/public/img/signature/<?=$image?>.webp')"></div>
            </label>
<?
        }
?>
            </div>
            <input type='submit' value='Change'/>
        </form>
    </div>
</div>