<link rel="stylesheet" href="/public/css/news.css">
<? foreach ($latest_news as $news) { ?>
<div class="section news" id="<?=$news['id']?>">
    <div class="header">
        <div class="title" title="<?=$news['title']?>"><?=$news['title']?></div>
        <div class="date"><?=date('d-m-y, h:i', $news['time'])?></div>
    </div>
    <div class="content">
        <div class="author">
        <div class="avatar" <?=$news['avatar'] ? 'style="--image: url(/public/img/avatar/' . $news['avatar'] . '.webp);"' : ''?>></div>
            <div class="nickname" title="<?=$news['nickname']?>"><?=$news['nickname']?></div>
        </div>
        <div class="message">
            <?=bbcode_to_html($news['message'])?>
        </div>
    </div>
</div>
<? } ?>